<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        <span class="text-h6 text-bold">Manage Sellers and Commissions</span><br/>
                        <span class="text-caption text-grey">Sellers List and Commissions for each item unit to be reffer or sold</span><br/>

                        <div class="text-right">
                            <q-btn-group rounded>
                                <q-btn color="primary" rounded glossy icon="ti-plus" label="Add User" no-caps @click="addItemModal = !addItemModal" />
                            </q-btn-group>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-input 
                    outlined
                    rounded
                    bottom-slots 
                    dense
                    v-model="search" 
                    placeholder="Search User" 
                >
                    <template v-slot:prepend>
                        <q-icon name="ti-search" />
                    </template>
                    <template v-slot:append>
                        <q-icon
                            v-show="search != ''"
                            name="close" 
                            @click="search = ''" 
                            class="cursor-pointer" 
                        />
                    </template>
                </q-input>
            </div>

            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                
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
                    flat bordered
                    :rows="itemsList"
                    wrap-cells
                    :columns="columns"
                    row-key="name"
                    separator="cell"
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
                                {{ col.value }}
                            </q-td>
                        </q-tr>
                    </template>
                </q-table>
            </div>
        </div>



        <!-- Modals -->
        <AddSellerModal
            :modalStatus="addItemModal"  
            @updateModalStatus="addItemModal = !addItemModal"
            @updateList="fetchProductList"
        />
    </div>
</template>

<script>
import moment from 'moment'
import AddSellerModal from "../../components/Modals/AddUserModal.vue"
import getQueryStock from '../../firebase/firebase-query-stock'


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'OrderPage',
    components:{
        AddSellerModal
    },
    data(){
        return {
            addItemModal: false,
            tableLoading: false,
            search:"",
            itemsList: []
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
                { name: 'email', align: 'left', label: 'Username', field: 'email' },
                { name: 'contact', align: 'left', label: 'Contact Number', field: 'contact' },
                { name: 'userType', label: 'Type', field: 'userType', sortable: true },
                { name: 'status', label: 'Status', field: 'status', sortable: true },
            ]
        }
    },
    watch: {
        tab(){
            this.fetchProductList();
        }
    },
    created(){
        this.fetchProductList();
    },
    methods: {
        moment,
        async fetchProductList(){
            this.tableLoading = true
            this.itemsList = [];

            try {
                const res = await getQueryStock(`userProfile`)
                this.itemsList = res;
                this.tableLoading = false
            } catch (error) {
                console.log(error)
                this.$q.notify({
                    message: 'Error on fetching order list',
                    color: 'negative',
                });
            }
        },
        convertCurrency(value){
            let amount = Number(value);
            return amount.toLocaleString('en-US', {
                style: 'currency',
                currency: 'PHP',
            })
        },
    }
}
</script>
<style scoped>
.my-card{
    border-radius: 15px;
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
</style>
