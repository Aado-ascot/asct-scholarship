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
                        <span class="text-h6 text-bold">Inventory</span><br/>
                        <span class="text-caption text-grey">Products List and Manage product details</span><br/>

                        <div class="text-right">
                            <q-btn-group rounded>
                                <!-- <q-btn color="amber" rounded glossy icon="visibility" /> -->
                                <q-btn color="primary" rounded glossy icon="ti-plus" label="Add Product" no-caps @click="addItemModal = !addItemModal" />
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
                    v-model="search" 
                    placeholder="Search Product" 
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
        <InventoryModal
            :modalStatus="addItemModal"  
            @updateModalStatus="addItemModal = !addItemModal"
            @updateList="fetchProductList"
        />
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import InventoryModal from "../../components/Modals/InventoryModal.vue"
import listDocuments from '../../firebase/firebase-list'


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'InventoryPage',
    components:{
        InventoryModal
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
                    label: 'Product Unit',
                    align: 'left',
                    field: row => row.name,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'features', align: 'left', label: 'Features', field: 'features' },
                { name: 'type', label: 'Model Type', field: 'type', sortable: true },
                {
                    name: 'capacity',
                    required: true,
                    label: 'Capacity',
                    align: 'left',
                    field: row => row.capacity,
                    format: val => `${val} HP`,
                    sortable: true
                },
                {
                    name: 'srp',
                    required: true,
                    label: 'SRP (PHP)',
                    align: 'left',
                    field: row => row.srp,
                    format: val => `${this.convertCurrency(val)}`,
                    sortable: true
                },
            ]
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
                const res = await listDocuments(`productsInventory`)
                this.itemsList = res;
                this.tableLoading = false
            } catch (error) {
                console.log(error)
                this.$q.notify({
                    message: 'Error on fetching product list',
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
