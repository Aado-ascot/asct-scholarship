<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <span class="text-h6 text-bold">Hi {{`${user.fullName}`}},</span><br/>
                <span class="text-caption">Welcome to ASCOT Scholarship Application</span><br/>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-4 q-pa-sm">
                <q-card
                    flat
                    class="my-card active-scholar"
                >
                    <q-card-section>
                        <div class="row">
                            <div class="col-6">
                                <span class="text-h6">Active Scholarship</span><br/>
                                <span class="text-caption">Approved Scholarship: --</span><br/>
                                <span class="text-caption">Approved Date: --</span><br/>
                                <span class="text-caption">Link Details: --</span><br/>
                            </div>
                            <div class="col-6 text-right">
                                <q-img :src="`/imgs/ASCT_logo2.png`" />
                            </div>
                            <div class="col-12">
                                <div class="text-right">
                                    <q-btn flat color="white" size="sm" label="View Scholarship Details" />
                                </div>
                            </div>
                        </div>
                        
                        
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-8 q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        Submitted Application
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <div v-if="tableLoading && itemsList.length === 0" class="text-center">
                            <q-spinner-bars
                                color="primary"
                                size="3em"
                            />
                        </div>
                        <div 
                            v-if="!tableLoading && itemsList.length === 0" 
                            class="text-center q-pa-md"
                        >
                            <q-icon color="grey-4" name="ti-dropbox-alt" size="6em" /> <br/>
                            <span class="text-caption text-grey-8">
                                No Data Can Be Shown.
                            </span>
                        </div>
                        <q-table
                            v-if="itemsList.length > 0"
                            flat
                            bordered
                            :rows="itemsList"
                            wrap-cells
                            :columns="tableColumns"
                            row-key="name"
                            :filter="search"
                        >  
                            <template v-slot:header="props">
                                <q-tr :props="props">
                                    <q-th
                                        v-for="col in props.cols"
                                        :key="col.name"
                                        :props="props"
                                    >
                                        {{ col.label }}
                                    </q-th>
                                </q-tr>
                            </template>
                            <template v-slot:body="props">
                                <q-tr :props="props">
                                    <q-td
                                        v-for="col in props.cols"
                                        :key="col.name"
                                        :props="props"
                                    >
                                        <div v-if="col.label === 'Application Status'" class="text-center">
                                            <q-stepper
                                                :model-value="checkStepProcess(col.value)"
                                                ref="stepper"
                                                contracted
                                                color="orange"
                                                flat
                                                :id="`evaluatorProcess-${col.value.id}`"
                                                active-icon="mdi-cog-clockwise"
                                                active-color="orange"
                                                done-icon="mdi-check-all"
                                                done-color="green"
                                                class="customStepper"
                                            >
                                                <q-step
                                                    class="no-content"
                                                    :name="1"
                                                    :done="Number(col.value.evaluatedBy) > 0"
                                                >
                                                    
                                                </q-step>

                                                <q-step
                                                    class="no-content"
                                                    :name="2"
                                                    title="Create an ad group"
                                                    icon="mdi-check-decagram"
                                                    :done="Number(col.value.approvedBy) > 0"
                                                >
                                                </q-step>
                                            </q-stepper>
                                            <q-tooltip
                                                anchor="center middle" self="top middle"
                                                :target="`#evaluatorProcess-${col.value.id}`"
                                                class="bg-white text-black q-pa-md"
                                            >
                                                <span class="text-bold">Status:</span> {{props.row.status}} <br/>
                                                <span class="text-bold">Remarks:</span> {{props.row.remarks || 'No Remarks'}} <br/>
                                            </q-tooltip>
                                        </div>
                                        <div v-if="col.label !== 'Application Status'">
                                            {{ col.value }}
                                        </div>
                                    </q-td>
                                </q-tr>
                            </template>
                        </q-table>
                    </q-card-section>
                </q-card>
            </div>
            
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    data(){
        return {
            tableLoading: false,
            itemsList: [],
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        },
        tableColumns: function(){
            return [
                {
                    name: 'title',
                    required: true,
                    label: 'Scholarship Applied',
                    align: 'left',
                    field: row => row.title,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'provider', label: 'Type', field: 'provider', align: 'left' },
                { name: 'processFlow', label: 'Application Status', field: 'data', align: 'center',},
                { name: 'remarks', label: 'Remarks', field: 'remarks', align: 'left' },
            ]
        }
    },
    created(){
        this.getList()
    },
    methods: {
        moment,
        checkStepProcess(data){
            let res = 1;

            if(Number(data.evaluatedBy) !== 0 && Number(data.approvedBy) === 0){
                res = 2
            } else if(Number(data.evaluatedBy) > 0 && Number(data.approvedBy) > 0){
                res = 3
            }
            return res
        },
        async getList(){
            this.tableLoading = true
            this.itemsList = []

            this.$api.post('scholarship/applied/status', {sId: this.user.userId}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsList = data.list
                } else {
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'report_problem'
                    })
                }

                this.tableLoading = false
            })
        },
    }
}
</script>
<style scoped>
.customStepper{
    
    padding: 0px !important;
}
.q-stepper__header--contracted {
    min-height: 32px!important;
}
.no-content{
    display: none;
}
.my-card{
    border-radius: 15px;
    box-shadow: 0px 0px 3px -2px !important;
}
.active-scholar{
    height: 150px;
    background: rgb(0,177,250);
    background: linear-gradient(67deg, rgb(255, 255, 255) 0%, rgba(17, 140, 240, 0.966) 100%);
}
.my-card-item{
    border-radius: 10px;
}
.generatedDash{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(255 251 176) 0%, rgb(0 55 255 / 61%) 89%);
}
.generatedDash2{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(38 148 243) 0%, rgb(45 253 215 / 61%) 89%);
}
</style>
