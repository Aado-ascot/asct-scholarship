<template>
    <div class="q-pa-sm" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12  q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        <span class="text-h6 text-bold">Customers</span><br/>
                        <span class="text-caption text-grey">Manage customer's order units and transactions</span><br/>

                        <div class="text-right">
                            <q-btn-group rounded>
                                <!-- <q-btn color="amber" rounded glossy icon="visibility" /> -->
                                <q-btn color="primary" rounded glossy icon="ti-plus" label="Add Stock" no-caps @click="addItemModal = !addItemModal" />
                            </q-btn-group>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-xs-12 col-sm-6 col-md-3 q-pa-sm" style="height: 70dvh;">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        <q-input 
                            outlined
                            round
                            bottom-slots 
                            dense
                            v-model="search" 
                            placeholder="Search Customer" 
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
                        <q-list >
                            <settingsMenu
                                v-for="link in linksList"
                                :key="link.id"
                                v-bind="link"
                                @menuClicked="setMenuClicked"
                            />
                        </q-list>
                    </q-card-section>
                </q-card>
                
            </div>
            <div class="col-12 col-xs-12 col-sm-6 col-md-9 q-pa-sm" style="height: 70dvh;">
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
                        No Item can be shown.
                    </span>
                </div>

                <div v-if="itemsList.length > 0" class="row my-card q-pa-md" style="height: 70dvh;">
                    <div 
                        v-for="(item, idx) in itemsList" 
                        :key="idx" 
                        class="col-12 prod-item"
                    >
                        <q-icon color="blue" name="mdi-snowflake" size="1.5em" />
                        {{item.name}} {{item.capacity}}
                    </div>
                </div>
            </div>
        </div>



        <!-- Modals -->
        <OrderModal
            :modalStatus="addItemModal"  
            @updateModalStatus="addItemModal = !addItemModal"
            @updateList="fetchProductList"
        />
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import OrderModal from "../../components/Modals/AddOrderModal.vue"
import getQueryStock from '../../firebase/firebase-query-stock'
import settingsMenu from '../../components/Common/SettingMenu.vue'


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'OrderPage',
    components:{
        OrderModal,
        settingsMenu
    },
    data(){
        return {
            addItemModal: false,
            tableLoading: false,
            search:"",
            tab: "In-progress",
            itemsList: [],
            linksList: []
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
                {
                    name: 'capacity',
                    required: true,
                    label: 'Capacity',
                    align: 'left',
                    field: row => row.capacity,
                    format: val => `${val} HP`,
                    sortable: true
                },
                { name: 'modelNo', align: 'left', label: 'Model Number', field: 'modelNo' },
                { name: 'unitType', label: 'Model Type', field: 'unitType', sortable: true },
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
            this.linksList = [];

            try {
                const res = await getQueryStock(`customerInfo`)
                this.linksList = res;
                this.tableLoading = false
            } catch (error) {
                console.log(error)
                this.$q.notify({
                    message: 'Error on fetching order list',
                    color: 'negative',
                });
            }
        },
        async setMenuClicked(id){
            this.itemsList = [];

            try {
                const res = await getQueryStock(`orderProduct`, 'customerId', id)
                this.itemsList = res;
                console.log(res)
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
.prod-item{
    cursor: pointer;
    height: 1.5rem;
    border-radius: 4px;
}
.prod-item:hover{
    background-color: rgb(199, 196, 196);
}
</style>
