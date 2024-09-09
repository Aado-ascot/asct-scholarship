<template>
    <div class="q-pa-sm">
        <q-dialog persistent v-model="openModal">
            <q-card style="width: 70dvw; max-width: 90dvw;">
                <q-card-section>
                    <div class="text-h6">ORDER INFORMATION</div>
                </q-card-section>

                <q-separator />

                <q-card-section style="max-height: 60vh; height: 50dvh;" class="scroll">
                    <q-stepper
                        v-model="step"
                        ref="stepper"
                        color="primary"
                        animated
                        class="shadow-0 q-mt-md"
                    >
                        <q-step
                            :name="1"
                            title="Customer Information"
                            icon="ti-user"
                            :done="step > 1"
                        >
                            <div class="row">
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        v-model="form.customerInfo.lastName" 
                                        label="Last Name" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        v-model="form.customerInfo.firstName" 
                                        label="First Name" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        v-model="form.customerInfo.middleName" 
                                        label="Middle Name" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        v-model="form.customerInfo.suffix" 
                                        label="Suffix" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        v-model="form.customerInfo.contactNumber" 
                                        label="Contact Number" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        v-model="form.customerInfo.emailAddress" 
                                        label="Email" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-12 q-pa-sm">
                                    <q-input
                                        outlined
                                        type="textarea"
                                        v-model="form.customerInfo.address" 
                                        label="Address" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                            </div>
                        </q-step>
                        <q-step
                            :name="2"
                            title="Order Information"
                            icon="ti-dropbox"
                            :done="step > 2"
                        >
                            <div class="row">
                                <div class="col col-md-12">
                                    <span class="text-h6 q-mr-lg">Schedule Details</span>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        type="date"
                                        v-model="form.scheduleInfo.dateOfDelivery" 
                                        label="Delivery Date" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        v-model="form.scheduleInfo.landMark" 
                                        label="Landmark" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-select
                                        v-model="form.salesmanDetails"
                                        :options="sellerOpt"
                                        label="Seller"
                                        dense
                                        outlined
                                        :options-dense="true"
                                    >
                                    </q-select>
                                </div>
                                <div class="col-12 col-md-12 q-pa-sm">
                                    <q-input
                                        outlined 
                                        type="textarea"
                                        v-model="form.scheduleInfo.remarks" 
                                        label="Remarks" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col col-md-12">
                                    <span class="text-h6 q-mr-lg">Order Details</span>
                                </div>
                                <div class="col-12">
                                    <div class="row">
                                        <div 
                                            v-for="(item, idx) in form.order"
                                            :key="idx"
                                            class="col-3 q-pa-xs "
                                        >
                                            
                                            <div class=" productItem  q-pa-sm">
                                                <div class="fit row wrap justify-between content-between">
                                                    <q-icon name="mdi-air-conditioner" size="20px"  color="grey" />
                                                    <q-btn
                                                        :disabled="idx === 0"
                                                        @click="removeId(item)"
                                                        color="red"
                                                        round 
                                                        dense 
                                                        flat 
                                                        size="sm" 
                                                        icon="ti-trash" 
                                                    />
                                                </div>
                                                <q-input
                                                    dense
                                                    icon="ti-pencil"
                                                    v-model="item.name"
                                                    label="Product Name *"
                                                >
                                                    <template v-slot:append>
                                                        <q-icon
                                                            v-if="!popupLoading && popupItems.length === 0"
                                                            name="ti-plus" 
                                                            @click="fetchProductList(idx)"
                                                            class="cursor-pointer" 
                                                        />
                                                        <q-spinner-ball
                                                            v-if="popupLoading && orderIndex === idx"
                                                            color="primary"
                                                        />
                                                        <q-icon
                                                            v-if="!popupLoading && popupItems.length > 0 && orderIndex === idx"
                                                            name="ti-close" 
                                                            @click="popupItems = []"
                                                            class="cursor-pointer" 
                                                        />
                                                    </template>
                                                </q-input>
                                                <div 
                                                    v-if="popupItems.length > 0 && orderIndex === idx"
                                                    class="col-12 col-xs-6 col-md-6 q-pa-sm q-mt-xs capacity-border popupItems"
                                                >
                                                    <q-list separator>
                                                        <q-item 
                                                            v-for="(pitem) in popupItems"
                                                            :key="pitem"
                                                            clickable
                                                            @click="populateProductInformation(pitem, idx)"
                                                            v-ripple
                                                        >
                                                            <q-item-section>{{pitem}}</q-item-section>
                                                        </q-item>
                                                    </q-list>
                                                </div>
                                                <q-select
                                                    v-model="item.capacity"
                                                    :options="capacityOpt"
                                                    label="Capacity (Horse Power)"
                                                    dense
                                                    :options-dense="true"
                                                >
                                                </q-select>
                                            </div>
                                            
                                        </div>
                                        <div 
                                            class="col-3"
                                            @click="addItem"
                                        >
                                            <div class="add-product-button q-ma-xs">
                                                <q-icon name="mdi-package-variant-closed-plus" size="5rem" color="grey" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col col-md-12">
                                    <span class="text-h6 q-mr-lg">Payment Details</span>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        v-model="form.scheduleInfo.payDescription" 
                                        label="Payment Description" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-input
                                        outlined 
                                        v-model="form.scheduleInfo.payAmount" 
                                        label="Payment Amount" 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-4 q-pa-sm">
                                    <q-select
                                        v-model="form.scheduleInfo.modeOfPayment"
                                        :options="paymenMethodOpt"
                                        label="Payment Method"
                                        dense
                                        outlined
                                        :options-dense="true"
                                    >
                                    </q-select>
                                </div>
                                
                            </div>
                        </q-step>
                        <q-step
                            :name="3"
                            title="Summary"
                            icon="ti-layout-cta-center"
                        >
                            <div class="row">
                                <div class="col-12 q-pa-xs">
                                    <span class="text-bold text-h5 text-title">Order Details</span>
                                    <q-separator />
                                </div>
                                <div class="col-4 q-pa-xs">
                                    <span class="text-title text-bold">{{ `${form.scheduleInfo.dateOfDelivery}` }}</span><br/>
                                    <span class="text-caption text-grey">Delivery Date</span>
                                </div>
                                <div class="col-4 q-pa-xs">
                                    <span class="text-title">{{ `${form.scheduleInfo.landMark}` }}</span><br/>
                                    <span class="text-caption text-grey">Delivery Landmark</span>
                                </div>
                                <div class="col-4 q-pa-xs">
                                    <span class="text-title">{{ `${form.scheduleInfo.remarks}` }}</span><br/>
                                    <span class="text-caption text-grey">Delivery Remarks</span>
                                </div>
                                <div class="col-4 q-pa-xs">
                                    <span class="text-title">{{ `${form.salesmanDetails.label}` }}</span><br/>
                                    <span class="text-caption text-grey">Salesman</span>
                                </div>

                                <div class="col-4 q-pa-xs">
                                    <span class="text-title">{{ `${form.scheduleInfo.payAmount || '--'}` }}</span><br/>
                                    <span class="text-caption text-grey">Cash On Hand</span>
                                </div>
                                <div class="col-4 q-pa-xs">
                                    <span class="text-title">{{ `${form.scheduleInfo.payDescription}` }}</span><br/>
                                    <span class="text-caption text-grey">Payment Description</span>
                                </div>
                                
                                <div class="col-12 q-pa-xs">
                                    <span class="text-bold text-h6 text-title">Ordered Units</span>
                                    <div class="row">
                                        <div 
                                            v-for="(item, idx) in form.order"
                                            :key="idx"
                                            class="col-3 q-pa-xs "
                                        >
                                            
                                            <div class="productItem  q-pa-sm">
                                                <div class="fit wrap justify-between content-between">
                                                    <q-icon name="mdi-air-conditioner" size="20px"  color="grey" />
                                                    {{item.name}}
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                



                                
                                <div class="col-12 q-pa-xs q-mt-md">
                                    <span class="text-title text-bold text-h5">Customer Details</span>
                                    <q-separator />
                                </div>
                                <div class="col-4 q-pa-xs">
                                    <span class="text-title text-bold">{{ `${form.customerInfo.lastName || "--" }, ${form.customerInfo.firstName || "--" } ${form.customerInfo.suffix}, ${form.customerInfo.middleName || "--"}` }}</span><br/>
                                    <span class="text-caption text-grey">Customer Name</span>
                                </div>
                                <div class="col-4 q-pa-xs">
                                    <span class="text-title text-bold">{{ `${form.customerInfo.contactNumber}`  || "N/A"}}</span><br/>
                                    <span class="text-caption text-grey">Contact Number</span>
                                </div>
                                <div class="col-4 q-pa-xs">
                                    <span class="text-title"><strong>{{ form.customerInfo.emailAddress || "N/A" }}</strong></span><br/>
                                    <span class="text-caption text-grey">Email Address</span>
                                </div>
                                <div class="col-12 q-pa-xs">
                                    <span class="text-title text-bold">{{ form.customerInfo.address || "N/A" }}</span><br/>
                                    <span class="text-caption text-grey">Address</span>
                                </div>


                               
                            </div>
                        </q-step>
                    </q-stepper>
                </q-card-section>

                <q-separator />

                <q-card-actions >
                    <q-btn flat label="Close" color="negative" @click="closeModal" />
                    <q-space />
                    <q-btn v-if="step > 1" flat label="Back" color="primary" @click="$refs.stepper.previous()" />
                    <q-btn v-if="step < 3" flat label="Next" color="primary" @click="$refs.stepper.next()" />
                    <q-btn v-if="step === 3" flat label="Submit" :loading="btnLoading" color="positive" @click="submitForm" />
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
import createOrderProduct from '../../firebase/firebase-create-order';
import listProductsDocuments from '../../firebase/firebase-product-list';
import listDocuments from '../../firebase/firebase-list';

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
            step: 1,
            selectedScan: "",
            showReader: false,
            btnLoading: false,
            openModal: false,
            popupLoading: false,
            popupItems: [],
            sellerList: [],
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
                order: [
                    {
                        prodID: "",
                        name: "",
                        capacity: "",
                        price: "",
                        discount: "",
                        totalAmount: "",
                    }
                ],
                salesmanDetails: {
                    label: "",
                    value: "",
                },
                scheduleInfo: {
                    isNumber: "",
                    drNumber: "",
                    modeOfPayment: "",
                    payAmount: "",
                    payDescription: "",
                    dateOfDelivery: "",
                    remarks: "",
                    landMark: "",
                    status: "In-progress",
                },
                customerInfo: {
                    firstName: "",
                    middleName: "",
                    lastsName: "",
                    suffix: "",
                    emailAddress: "",
                    contactNumber: "",
                    address: "",
                }
            },
            orderIndex: 0,
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
            ],
            paymenMethodOpt: [
                'COD',
                'Partial',
                'Installment',
                'CC',
                'Cheque',
                'Bank Transfer',
                'Cash'
            ]
        }
    },
    watch:{
        modalStatus: function(newVal){
            this.openModal = newVal
            if(newVal){
                this.fetchSalesPersonList();
            }
        },
    },
    computed: {
        userDetails(){
            const details = LocalStorage.getItem('user')
            return details;
        },
        sellerOpt(){
            let res = []

            res = this.sellerList.map((el) => {
                return {
                    label: `${el.firstName} ${el.lastName}`,
                    value: el.id
                }
            })

            return res
        }
    },
    methods: {
        async fetchProductList(index){
            try {
                this.orderIndex = index
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
        async fetchSalesPersonList(){
            try {
                const res = await listDocuments(`sellerList`)
                this.sellerList = res
            } catch (error) {
                console.log(error)
                this.$q.notify({
                    message: 'Error on fetching product list',
                    color: 'negative',
                });
            }
        },
        populateProductInformation(val, index){
            const item = this.productList[val];
            this.form.order[index].prodID = item.id
            this.form.order[index].name = item.name

            this.capacityOpt = item.capacities.map((el) => {
                return el.capcity
            })
            
            this.form.order[index].capacity = this.capacityOpt[0]

            this.popupItems = []
        },
        async closeModal(){
            this.step = 1
            this.form = {
                order: [
                    {
                        isNumber: "",
                        drNumber: "",
                        prodID: "",
                        name: "",
                        capacity: "",
                        price: "",
                        discount: "",
                        totalAmount: "",
                    }
                ],
                salesmanDetails: {
                    label: "",
                    value: "",
                },
                scheduleInfo: {
                    dateOfDelivery: "",
                    modeOfPayment: "",
                    remarks: "",
                    landMark: "",
                    status: "In-progress",
                },
                customerInfo: {
                    firstName: "",
                    middleName: "",
                    lastsName: "",
                    suffix: "",
                    contactNumber: "",
                    address: "",
                }
            }
            this.sellerList = []
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
        addItem(){
            this.form.order.push({
                        prodID: "",
                        name: "",
                        capacity: "",
                        price: "",
                        discount: "",
                        totalAmount: "",
                    })
        },
        closeReader(){
            this.showReader = false;
        },
        removeId(item){
            const index = this.form.order.indexOf(item);
            this.form.order.splice(index, 1)
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
                let payload = {
                    ...this.form
                }
                createOrderProduct('orderProduct', payload);

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
.productItem {
    border: 1px solid grey;
}
.add-product-button{
    cursor: pointer;
    width: 100%;
    height: 121.6px;
    border: 1px dashed grey;
    text-align: center;
    display: flex;         /* NEW, Spec - Firefox, Chrome, Opera */
    justify-content: center;
    align-items: center;
}
</style>