<template>
    <div>
        <q-dialog
            class="modalIndex"
            v-model="openModal"
            persistent
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card style="width: 85dvw; max-width: 85dvw;" >
                <q-bar class="q-mb-md">
                    <div class="text-h6">Add Check Voucher</div>
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section style="max-height: 70vh; height: 100%;" class="q-pt-none scroll">
                    <q-form
                        ref="formDetails"
                        class="row"
                    >   
                        <div class="col-12 col-md-12">
                            <span class="text-h6">Check Details</span>
                            <!-- <q-separator /> -->
                        </div>
                        <div class="col-12 col-md-4 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.cvNumber" 
                                label="CV No." 
                                stack-label 
                                dense
                            >
                                <template v-slot:prepend>
                                    <q-icon name="ti-layout-cta-left" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col-12 col-md-4 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.checkNumber" 
                                label="Check No." 
                                stack-label
                                dense
                            >
                                <template v-slot:prepend>
                                    <q-icon name="ti-stamp" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col-12 col-md-4 q-pa-sm">
                            <q-input
                                type="date"
                                outlined 
                                v-model="form.checkDate" 
                                label="Check Date" 
                                stack-label
                                dense
                            />
                        </div>
                        <div class="col-12 col-md-12 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.payee" 
                                label="Payee" 
                                stack-label
                                dense
                            >
                                <template v-slot:prepend>
                                    <q-icon name="ti-layout-media-overlay" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col-12 col-md-12 q-pa-sm">
                            <q-input
                                type="textarea"
                                outlined 
                                v-model="form.particulars" 
                                label="Particulars" 
                                stack-label
                                dense
                            />
                        </div>
                        <div class="col-12 col-md-6 q-pa-sm">
                            <q-select 
                                outlined 
                                v-model="form.bankId" 
                                :options="bankOpt" 
                                label="Bank" 
                                stack-label 
                                dense
                                options-dense
                            >
                                <template v-slot:prepend>
                                    <q-icon name="ti-credit-card" />
                                </template>
                            </q-select>
                        </div>
                        <div class="col-12 col-md-6 q-pa-sm">
                            <q-input
                                type="number"
                                outlined 
                                v-model="form.cashInBank" 
                                label="Cash In Bank" 
                                stack-label
                                dense
                            >
                                <template v-slot:prepend>
                                    <q-icon name="ti-wallet" />
                                </template>
                            </q-input>
                        </div>
                        <div class="col-12 col-md-3 q-pa-sm">
                            <q-input
                                type="number"
                                outlined 
                                v-model="form.expandedWTax" 
                                label="Expanded Withholding Tax" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-3 q-pa-sm">
                            <q-input
                                type="number"
                                outlined 
                                v-model="form.inputTax" 
                                label="Input Tax" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-3 q-pa-sm">
                            <q-input
                                type="number"
                                outlined 
                                v-model="form.transAndTravel" 
                                label="Transportation & Travel" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-3 q-pa-sm">
                            <q-input
                                type="number"
                                outlined 
                                v-model="form.utilities" 
                                label="Communication, Light & Water" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        
                        <div class="col col-md-12 q-mt-md">
                            <span class="text-h5">Account Titles Details</span>
                            <!-- <q-separator /> -->
                        </div>
                        <div class="col col-md-6 q-pa-md">
                            <q-btn outline color="primary" no-caps label="Add Account Title" icon="ti-plus" class="full-width dashed-border" @click="addAccountTitle" />
                            <div
                                v-for="(itm, idx) in form.titleDetails"
                                :key="idx"
                                class="row q-mt-sm"
                            >
                                <div class="col-12 col-md-1 q-pa-xs  justify-center items-center content-center">
                                   <q-btn @click="removeId(idx)" size="sm" round color="red" icon="ti-trash" />
                                </div>
                                <div class="col-12 col-md-5 q-pa-xs">
                                    <q-select 
                                        outlined
                                        v-model="itm.titleId"
                                        :options="accountTitles" 
                                        label="Account Title"
                                        stack-label 
                                        dense
                                        use-input
                                        input-debounce="0"
                                        @filter="filterFn"
                                        options-dense
                                    >
                                        <template v-slot:prepend>
                                            <q-icon name="ti-smallcap" />
                                        </template>
                                    </q-select>
                                </div>
                                <div class="col-12 col-md-3 q-pa-xs">
                                    <q-input
                                        type="number"
                                        outlined 
                                        v-model="itm.dr" 
                                        label="Dr." 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                                <div class="col-12 col-md-3 q-pa-xs">
                                    <q-input
                                        type="number"
                                        outlined 
                                        v-model="itm.cr" 
                                        label="Cr." 
                                        stack-label 
                                        dense
                                    >
                                    </q-input>
                                </div>
                            </div>
                        </div>
                        <div class="col col-md-6 q-pa-md">
                            <span class="text-title text-bold">Check & Balances</span>
                            <div
                                class="row q-mt-sm"
                            >
                                <div class="col-6 col-md-6 q-pa-xs">
                                    Total Dr.
                                </div>
                                <div class="col-6 col-md-6 q-pa-xs">
                                    Value
                                </div>
                                <div class="col-6 col-md-6 q-pa-xs">
                                    Total Cr.
                                </div>
                                <div class="col-6 col-md-6 q-pa-xs">
                                    Value
                                </div>
                                <div class="col-6 col-md-6 q-pa-xs">
                                    Check Balance
                                </div>
                                <div class="col-6 col-md-6 q-pa-xs">
                                    Value (True/False)
                                </div>
                            </div>
                        </div>
                    </q-form>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn
                        flat 
                        label="Save as Draft" 
                        color="red"
                    />
                    <q-btn
                        flat 
                        label="Create Voucher" 
                        color="primary"
                        @click="submitModalClick" 
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>

    </div>
</template>
<script>
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode'
import { api } from 'boot/axios'

export default {
    name: 'AddCheckVoucherModal',
    data(){
        return {
            splitterModel: 40,
            splitterInfoModel: 20,
            openModal: false,
            openSignModal: false,
            form: {
                cvNumber: '',
                checkDate: '',
                payee: '',
                particulars: '',
                bankId: '',
                checkNumber: '',
                cashInBank: '',
                expandedWTax: '',
                inputTax: '',
                transAndTravel: '',
                utilities: '',
                createdBy: '',
                checkStatus: '',
                checkBalance: '',
                totalDr: '',
                totalCr: '',
                titleDetails: []
            },
            accountTitles: [],
            originalTitles: [],
            bankOpt: [],
        }
    },
    watch:{
        modalStatus(newVal, oldVal){
            this.openModal = newVal
            this.getTitles().then(() => {
                this.getBanks()
            })
            if(this.processType === "UPDATE" && newVal){
                this.fillExistingData().then(() => {
                    this.getProfile()
                })
            } else {
                this.clearForm()
            }
        }
    },
    props: {
        appData: {
            type: Object
        },
        modalStatus: {
            type: Boolean
        },
        processType: {
            type: String
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        }
    },

    methods: {
        async getTitles(){
            const vm = this
            this.$api.get('misc/get/titles').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.accountTitles = response.data.map((obj) => {
                        return {
                            label: obj.title,
                            value: obj.id,
                        }
                    })
                    this.originalTitles = response.data.map((obj) => {
                        return {
                            label: obj.title,
                            value: obj.id,
                        }
                    })
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        message: data.title,
                        caption: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                }
            })
        },
        async getBanks(){
            const vm = this
            this.$api.get('misc/get/banks').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.bankOpt = response.data.map((obj) => {
                        return {
                            label: `${obj.bankAbbr} (${obj.bankName})`,
                            value: obj.id,
                        }
                    })
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                }
            })
        },
        async fillExistingData(){
            for(const key in this.form){
                this.form[key] = this.appData[key]
            }
        },
        addAccountTitle(){
            this.form.titleDetails.push({
                titleId: '',
                dr: '',
                cr: ''
            })
        },
        filterFn (val, update, abort) {
            update(() => {
                const needle = val.toLowerCase()
                this.accountTitles = this.originalTitles.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
            })
        },
        removeId(index){
            this.form.titleDetails.splice(index, 1)
        },
        async closeModal(){
            this.$emit('updateModalStatus', false);
        },
        async submitModalClick(){
            let vm = this;

            this.$refs.formDetails.validate().then(success => {
                if(!success){
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title: 'Incomplete Form',
                        message: 'Please fill the required fields',
                        icon: 'report_problem'
                    })
                    return false;
                } else {
                    // Confirm
                    this.$q.dialog({
                        title: 'Submit details',
                        message: 'Would you like to finalize and save your data?',
                        ok: {
                            label: 'Yes'
                        },
                        cancel: {
                            label: 'No',
                            color: 'negative'
                        },
                        persistent: true
                    }).onOk(() => {
                        // this.$emit('submitModalClick', vm.form);
                        this.addNewVoucher();
                    })
                }
            })
            
        },

        async addNewVoucher(){
            // this.$q.loading.show();
            let payload = this.form
            payload.createdBy = this.user.userId
            
            // console.log(payload)
            // return
            
            api.post('voucher/create', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$emit('refreshData')
                    this.clearForm();
                    this.closeModal();
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                }

            })

            // this.$q.loading.hide();
        },
        

        clearForm(){
            this.form = {
                cvNumber: '',
                checkDate: '',
                payee: '',
                particulars: '',
                bankId: '',
                checkNumber: '',
                cashInBank: '',
                expandedWTax: '',
                inputTax: '',
                transAndTravel: '',
                utilities: '',
                createdBy: '',
                checkStatus: '',
                checkBalance: '',
                totalDr: '',
                totalCr: '',
                titleDetails: []
            }
        },
    }
    
}
</script>

<style>
.hydrated{
    z-index: 9999999 !important;
}
.dashed-border {
    border: 1px dashed !important;
}
</style>