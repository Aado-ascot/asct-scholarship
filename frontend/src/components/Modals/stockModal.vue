<template>
    <div class="q-pa-sm">
        <q-dialog persistent v-model="openModal">
            <q-card style="width: 700px; max-width: 80vw;">
                <q-card-section>
                    <div class="text-h6">ADD PRODUCT STOCK</div>
                </q-card-section>

                <q-separator />

                <q-card-section style="max-height: 60vh; height: 50dvh;" class="scroll">
                    <q-form
                        ref="formDetails"
                        class="row"
                    >
                        <q-input
                            class="col-12 col-xs-12 col-md-12 q-pa-sm q-mt-sm"
                            dense
                            icon="ti-pencil"
                            v-model="form.name"
                            label="Product Name *"
                        >
                            <template v-slot:prepend>
                                <q-icon name="ti-package" @click.stop />
                            </template>
                            <template v-slot:append>
                                <q-icon
                                    v-if="!popupLoading && popupItems.length === 0"
                                    name="ti-plus" 
                                    @click="fetchProductList"
                                    class="cursor-pointer" 
                                />
                                <q-spinner-ball
                                    v-if="popupLoading"
                                    color="primary"
                                />
                                <q-icon
                                    v-if="!popupLoading && popupItems.length > 0"
                                    name="ti-close" 
                                    @click="popupItems = []"
                                    class="cursor-pointer" 
                                />
                            </template>
                        </q-input>
                        <!-- Popup select product -->
                        <div 
                            v-if="popupItems.length > 0"
                            class="col-12 col-xs-12 col-md-12 q-pa-sm q-mt-sm capacity-border popupItems"
                        >
                            <q-list separator>
                                <q-item 
                                    v-for="(item) in popupItems"
                                    :key="item"
                                    clickable
                                    @click="populateProductInformation(item)"
                                    v-ripple
                                >
                                    <q-item-section>{{item}}</q-item-section>
                                </q-item>
                            </q-list>
                        </div>
                        

                        <div class="col-12 col-xs-12 col-md-6 q-pa-md text-center justify-center items-center content-center">

                        </div>
                        <div class="col-12 col-xs-12 col-md-12 text-center justify-center items-center content-center">
                            Product Count: {{inventory.length}}
                        </div>
                        <div class="col-12 capacity-border q-pa-sm">
                            <div 
                                v-if="form.name === ''" 
                                class="text-center q-pa-md"
                            >
                                <q-icon color="grey-4" name="ti-alert" size="2em" /> <br/>
                                <span class="text-caption text-grey-8">
                                    No Capacity Selected.
                                </span>
                            </div>

                            <div  v-if="form.name !== ''">
                                <div
                                    v-for="(item, index) in inventory"
                                    :key="index"
                                    class="row"
                                >
                                    <div class="col-2 col-sm-1 col-md-1 text-center justify-center items-center content-center">
                                        <q-btn
                                            @click="showBarcodeReader(index)"
                                            color="primary"
                                            round 
                                            dense 
                                            flat 
                                            icon="qr_code_scanner" 
                                        />
                                    </div>
                                    <div class="col-10 col-sm-6 col-md-3 q-pa-xs">
                                        <q-input
                                            outlined
                                            dense
                                            v-model="item.modelNo"
                                            label="Model Name"
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-4 q-pa-xs">
                                        <q-select
                                            v-model="item.capacity"
                                            :options="capacityOpt"
                                            label="Capacity (Horse Power)"
                                            dense
                                            outlined
                                            :options-dense="true"
                                        >
                                            <template v-slot:prepend>
                                                <q-icon name="ti-dashboard" @click.stop />
                                            </template>
                                        </q-select>
                                    </div>
                                    <div class="col-10 col-sm-6 col-md-3 q-pa-xs">
                                        <q-select
                                            v-model="item.unitType"
                                            :options="typeOption"
                                            label="Unit Type"
                                            dense
                                            outlined
                                            :options-dense="true"
                                        >
                                            <template v-slot:prepend>
                                                <q-icon name="ti-harddrive" @click.stop />
                                            </template>
                                        </q-select>
                                    </div>
                                    <div class="col-2 col-sm-5 col-md-1 q-pa-xs text-center justify-center items-center content-center">
                                        <q-btn
                                            v-if="index === 0"
                                            @click="addUnit"
                                            color="secondary"
                                            round 
                                            dense 
                                            flat
                                            size="sm" 
                                            icon="ti-plus" 
                                        />
                                        <q-btn
                                            v-else
                                            @click="removeId(index)"
                                            color="red"
                                            round 
                                            dense 
                                            flat 
                                            size="sm" 
                                            icon="ti-trash" 
                                        />
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </q-form>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn flat label="Close" color="negative" @click="closeModal" />
                    <q-btn flat label="Submit" :loading="btnLoading" color="positive" @click="submitForm" />
                </q-card-actions>
            </q-card>
        </q-dialog>

        <BarcodeModal 
            :modalStatus="showReader"
            @generatedValue="addModelNo"
            @updateModalStatus="closeReader"
        />
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import BarcodeModal from './BarcodeModal.vue'
import addProductDocument from '../../firebase/firebase-add';
import listProductsDocuments from '../../firebase/firebase-product-list';

const dateNow = moment().format('MM/DD/YYYY');

export default{
    name: 'AddProductModal',
    components: {
        BarcodeModal
    },
    props: {
        appId: {
            type: Number
        },
        modalStatus: {
            type: Boolean
        },
        modalTitle: {
            type: String
        }
    },
    data(){
        return {
            selectedScan: "",
            showReader: false,
            btnLoading: false,
            openModal: false,
            popupLoading: false,
            popupItems: [],
            productList: {},
            maximizedToggle: true,
            capacityArr: "",
            capacitySrp: {},
            inventory: [
                {
                    modelNo: "",
                    unitType: "INDOOR",
                    capacity: "",
                    status: "In-stock"
                }
            ],
            form: {
                id: '',
                name: "",
            },
            typeOption: [
                "INDOOR",
                "OUTDOOR",
            ],
            capacityOpt: [
                "0.5",
                "0.8",
                "1.0",
                "1.5",
                "1.8",
                "2.0",
                "2.5",
                "2.8",
                "3.0",
                "3.5",
                "3.8"
            ]
        }
    },
    watch:{
        modalStatus: function(newVal){
            this.openModal = newVal
        },
        'form.unitPrice'(newVal){
            this.form.basePrice = Number(newVal) / Number(this.form.qty_unit) 
        },
        'form.qty_unit'(newVal){
            this.form.basePrice = Number(this.form.unitPrice) / Number(newVal) 
        }
    },
    computed: {
        userDetails(){
            const details = LocalStorage.getItem('user')
            return details;
        }
    },
    methods: {
        async fetchProductList(){
            try {
                this.popupLoading = true
                this.popupItems = []
                const res = await listProductsDocuments(`productsInventory`)
                for(const i in res){
                    this.popupItems.push(i)
                }
                this.productList = res
                this.popupLoading = false
            } catch (error) {
                console.log(error)
                this.$q.notify({
                    message: 'Error on fetching product list',
                    color: 'negative',
                });
            }
        },
        populateProductInformation(val){
            const item = this.productList[val];
            this.form.name = item.name
            this.form.id = item.id

            item.capacities.forEach((el) => {
                return this.capacitySrp[el.capcity] = el.srp
            })

            this.capacityOpt = item.capacities.map((el) => {
                return el.capcity
            })
            
            this.form.capacity = this.capacityOpt[0]

            this.popupItems = []
        },
        async closeModal(){
            this.inventory = [{
                modelNo: "",
                unitType: "INDOOR",
                status: "In-stock",
                capacity: "",
            }];
            this.form = {
                id: '',
                name: ""
            }
            this.$emit('updateModalStatus');
        },
        async onDecode(result){
            this.form.itemNumber = result
            this.showReader = false;
        },
        showBarcodeReader(index){
            this.selectedScan = index
            this.showReader = true;
        },
        addModelNo(modelNo){
            this.inventory[this.selectedScan].modelNo = modelNo
            this.showReader = false;
        },
        addUnit(){
            this.inventory.push({
                modelNo: "",
                unitType: "INDOOR",
                status: "In-stock",
                capacity: "",
            })
        },
        closeReader(){
            this.showReader = false;
        },
        removeId(item){
            this.inventory.splice(item, 1)
        },
        submitForm(){
            // Confirm
            this.$q.dialog({
                title: 'Save Data',
                message: 'Would you like to save your data?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                this.saveProductData();
            })
        },

        async saveProductData(){

            let vm = this;
            this.$q.loading.show({
                message: 'Your data is submitting. Please wait...'
            });
            this.btnLoading = true;

            try {
                await this.inventory.forEach(cap => {
                    let payload = {
                        ...this.form,
                        ...cap
                    }
                    addProductDocument('productStock', payload);
                });

                this.$q.loading.hide();
                this.$nextTick(() => {
                    this.$emit("updateList")
                    this.closeModal()
                })
            } catch (error) {
                console.log(error)
                this.$q.notify({
                    message: 'Error saving data',
                    color: 'negative',
                });
            }
            

            this.btnLoading = false;
        },

        // ending method
    },

}
</script>

<style scoped>
.capacity-border{
    border: 1px solid grey;
    border-radius: 7px;
}
.popupItems{
    position: absolute;
    top: 19%;
    background: white !important;
    z-index: 9999;
}
</style>