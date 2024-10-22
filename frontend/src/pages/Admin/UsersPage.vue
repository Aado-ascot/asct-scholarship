<template>
    <div class="q-pa-sm" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12  q-pa-sm">
                <q-card
                    flat
                    class="bg-white"
                >
                    <q-card-section class="fit row wrap justify-start items-center content-center">
                        <span class="text-h6 text-bold">
                            User Management
                            <br/>
                            <span class="text-caption text-grey">Manage user details and access</span>
                        </span>
                        
                        <q-space />
                        <div class="text-right">
                            <q-btn-group>
                                <!-- <q-btn color="amber" rounded glossy icon="visibility" /> -->
                                <q-btn color="primary" rounded  icon="ti-plus" label="Add New Employee" no-caps @click="addEmployee.modalStatus = !addEmployee.modalStatus" />
                            </q-btn-group>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
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
                            :rows="itemsList"
                            wrap-cells
                            :columns="columns"
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
                                    <q-th>
                                        Action
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
                                        {{ col.value }}
                                    </q-td>
                                    <q-td class="text-center">
                                        <q-btn
                                            @click="showPayroll(props.row)"
                                            flat 
                                            round 
                                            color="primary" 
                                            size="md" 
                                            icon="mdi-calculator-variant" 
                                        />
                                    </q-td>

                                </q-tr>
                            </template>
                        </q-table>
                    </q-card-section>
                </q-card>
            </div>

        </div>

        <!-- Modals -->
        
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
// import addEmployeeModal from "../../components/Modals/AddEmployeeModal.vue"

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'PayrollPage',
    components:{
        // addEmployeeModal
    },
    data(){
        return {
            itemsList: [],
        }
    },
    computed: {
        columns(){
            return [
                {
                    name: 'name',
                    required: true,
                    label: 'Name',
                    align: 'left',
                    field: row => row,
                    format: val => `${val.firstName} ${val.lastName}`,
                    sortable: true
                },
                { name: 'startDate', align: 'left', label: 'Hired Date', field: 'startDate' },
                { name: 'status', align: 'left', label: 'End Date', field: 'status', sortable: true },
                {
                    name: 'contact',
                    required: true,
                    label: 'Contact Number',
                    align: 'left',
                    field: row => row.contact,
                    format: val => `${val}`,
                    sortable: true
                },
                
            ]
        },
        
    },
    methods: {
        moment,
    }
}
</script>
<style scoped>
.my-card{
    border-radius: 10px;
    box-shadow: 0px 0px 3px -2px !important;
}
.my-card-item{
    border-radius: 5px;
    border: 1px solid gray;
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
.prod-item{
    cursor: pointer;
    height: 1.5rem;
    border-radius: 4px;
}
.prod-item:hover{
    background-color: rgb(199, 196, 196);
}
</style>
