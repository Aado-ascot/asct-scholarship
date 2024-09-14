<template>
    <div class="q-pa-sm">
        <q-dialog persistent v-model="openModal">
            <q-card style="width: 700px; max-width: 80vw;">
                <q-card-section>
                    <div class="text-h6">ADD PRODUCT</div>
                </q-card-section>

                <q-separator />

                <q-card-section style="max-height: 60vh" class="scroll">
                    <q-form
                        ref="formDetails"
                        class="row"
                    >
                        <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-mt-lg">
                            <span class="text-h6">ITEM DETAILS</span>
                        </div>
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

                        
                        <q-select
                            class="col-12 col-xs-12 col-md-6 q-pa-md"
                            v-model="form.type"
                            :options="typeOption"
                            label="Unit Type"
                            dense
                            :options-dense="true"
                        >
                            <template v-slot:prepend>
                                <q-icon name="scatter_plot" @click.stop />
                            </template>
                        </q-select>
                        <q-input
                            class="col-12 col-xs-12 col-md-6 q-pa-sm q-mt-sm"
                            dense
                            v-model="form.minStock"
                            label="Minimum Stock Level"
                        >
                            <template v-slot:prepend>
                                <q-icon name="ti-alert" @click.stop />
                            </template>
                        </q-input>
                        <q-input
                            class="col-12 col-xs-12 col-md-12 q-pa-sm q-mt-sm"
                            dense
                            v-model="form.origin"
                            label="Origin"
                        >
                            <template v-slot:prepend>
                                <q-icon name="ti-location-pin" @click.stop />
                            </template>
                        </q-input>
                        
                        <q-input
                            type="textarea"
                            class="col-12 col-xs-12 col-md-12 q-pa-sm q-mt-sm"
                            dense
                            v-model="form.features"
                            label="Features"
                        />
                        <q-select
                            class="col-12 col-xs-12 col-md-12 q-pa-md"
                            v-model="capacityArr"
                            :options="capacityOpt"
                            label="Capacity (Horse Power)"
                            dense
                            multiple
                            :options-dense="true"
                        >
                            <template v-slot:prepend>
                                <q-icon name="ti-dashboard" @click.stop />
                            </template>
                        </q-select>
                        
                        <div class="col-12 capacity-border q-pa-sm">
                            <div 
                                v-if="capacityArr.length === 0" 
                                class="text-center q-pa-md"
                            >
                                <q-icon color="grey-4" name="ti-alert" size="2em" /> <br/>
                                <span class="text-caption text-grey-8">
                                    No Capacity Selected.
                                </span>
                            </div>

                            <div
                                v-for="(item) in capacityArr"
                                :key="item"
                                class="row"
                            >
                                <div class="col-12 col-sm-6 col-md-6 text-center justify-center items-center content-center">
                                    <strong>{{`${item} HP`}}</strong>
                                </div>
                                <div class="col-12 col-sm-6 col-md-5 q-pa-xs">
                                    <q-input
                                        outlined
                                        dense
                                        v-model="capacitySrp[item]"
                                        label="SRP (PHP)"
                                    >
                                    </q-input>
                                </div>
                            </div>
                        </div>
                    </q-form>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" color="negative" @click="closeModal" />
                    <q-btn flat label="Submit" :loading="btnLoading" color="positive" @click="submitForm" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import addProductDocument from '../../firebase/firebase-add';
import listProductsDocuments from '../../firebase/firebase-product-list';

const dateNow = moment().format('MM/DD/YYYY');

export default{
    name: 'AddProductModal',
    components: {},
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
            showReader: false,
            btnLoading: false,
            openModal: false,
            popupLoading: false,
            popupItems: [],
            productList: {},
            maximizedToggle: true,
            capacityArr: [],
            capacitySrp: {},
            form: {
                name: "",
                type: "", //NON-INVERTER or INVERTER
                srp: "",
                origin: "",
                features: "",
                // Stock Validation
                minStock: "",
            },
            typeOption: [
                "NON-INVERTER",
                "INVERTER",
                "WINDOW NON-INVERTER",
                "WINDOW INVERTER",
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
            this.form = {
                name: item.name,
                type: item.type, //NON-INVERTER or INVERTER
                origin: item.origin,
                features: item.features,
                minStock: item.minStock,
            }

            item.capacities.forEach((el) => {
                return this.capacitySrp[el.capcity] = el.srp
            })

            this.capacityArr = item.capacities.map((el) => {
                return el.capcity
            })

            this.popupItems = []
        },
        async closeModal(){
            this.capacityArr = [];
            this.form =  {
                name: "",
                type: "", //NON-INVERTER or INVERTER
                srp: "",
                origin: "",
                features: "",

                // Stock Validation
                minStock: "",
            }
            this.$emit('updateModalStatus');
        },
        async onDecode(result){
            this.form.itemNumber = result
            this.showReader = false;
        },
        removeId(item){
            this.capacitySrp.splice(item, 1)
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
                await this.capacityArr.forEach(cap => {
                    let payload = {
                        ...this.form,
                        capacity: cap,
                        srp: vm.capacitySrp[cap]
                    }
                    addProductDocument('productsInventory', payload);
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