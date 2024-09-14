<template>
    <div class="q-pa-sm">
        <q-dialog persistent v-model="openModal">
            <q-card style="width: 700px; max-width: 80vw;">
                <q-card-section>
                    <div class="text-h6">ADD USER</div>
                </q-card-section>

                <q-separator />

                <q-card-section style="max-height: 60vh;" class="scroll">
                    <div class="row">
                        <div class="col-12 col-md-6 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.email" 
                                label="Email" 
                                stack-label 
                                hint="This was serve as the username to login"
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-6 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.password" 
                                label="Password" 
                                stack-label
                                type="password"
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-12 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.deviceIMEI" 
                                label="Device ID" 
                                stack-label
                                dense
                            >
                            </q-input>
                        </div>
                    </div>

                    <q-form
                        ref="formDetails"
                        class="row"
                    >
                        <div class="col-12 col-md-12 q-pa-sm">
                            <q-separator />
                        </div>
                        <div class="col-12 col-md-3 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.lastName" 
                                label="Last Name" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-3 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.firstName" 
                                label="First Name" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-3 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.middleName" 
                                label="Middle Name" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-3 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.suffix" 
                                label="Suffix" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-12 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.address" 
                                label="Address" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-6 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.contact" 
                                label="Contact Number" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-6 q-pa-sm">
                           <q-select 
                                outlined 
                                v-model="selectedType" 
                                :options="moduleOpt" 
                                label="User Type" 
                                stack-label 
                                dense
                                options-dense
                            >
                                <!-- <template v-slot:prepend>
                                    <q-icon name="ti-credit-card" />
                                </template> -->
                            </q-select>
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
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import register from '../../firebase/firebase-register';
import createDocument from '../../firebase/firebase-create';

const dateNow = moment().format('MM/DD/YYYY h:mm:ss A');

export default{
    name: 'AddUserModal',
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
            form: {
                firstName: "",
                lastName: "",
                middleName: "",
                suffix: "",
                address: "",
                contact: "",
                email: "",
                password: "",
                deviceIMEI: "",
                createdAt: dateNow,
                isDeleted: 0,
                moduleId: "",
                modules: "",
                status: 1,
                userType: "",
            },
            selectedType:"",
            moduleOpt:[
                {
                    label: "ADMIN",
                    value:"admin",
                    moduleId: 1,
                    modules: "101,107,108,109,110,111"
                },
                {
                    label: "INVENTORY",
                    value:"inventory",
                    moduleId: 1,
                    modules: "101,109,110"
                },
                {
                    label: "AGENT",
                    value:"agent",
                    moduleId: 2,
                    modules: "112,"
                },
            ]
        }
    },
    watch:{
        modalStatus: function(newVal){
            this.openModal = newVal
        }
    },
    computed: {
        userDetails(){
            const details = LocalStorage.getItem('user')
            return details;
        }
    },
    methods: {
        
        async closeModal(){
            this.form = {
                firstName: "",
                lastName: "",
                middleName: "",
                suffix: "",
                address: "",
                contact: "",
                email: "",
                password: "",
                deviceIMEI: "",
                createdAt: dateNow,
                isDeleted: 0,
                moduleId: "",
                modules: "",
                status: 1,
                userType: "",
            }
            this.$emit('updateModalStatus');
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
                    ...this.form,
                }
                payload.moduleId = this.selectedType.moduleId
                payload.modules = this.selectedType.modules
                payload.userType = this.selectedType.value

                register(payload).then((res)=>{
                    let deviceData = {
                        userId: res.uid,
                        deviceIMEI: payload.deviceIMEI
                    }
                    createDocument('registeredDevice', deviceData).then(() => {
                        this.$q.loading.hide();
                        this.$nextTick(() => {
                            this.$emit("updateList")
                            this.closeModal()
                        })
                    });
                    
                });

                
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