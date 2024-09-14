<template>
    <div class="q-pa-sm">
        <q-dialog persistent v-model="openModal">
            <q-card style="width: 700px; max-width: 80vw;">
                <q-card-section>
                    <div class="text-h6">ADD SELLER</div>
                </q-card-section>

                <q-separator />

                <q-card-section style="max-height: 60vh;" class="scroll">
                    <q-form
                        ref="formDetails"
                        class="row"
                    >
                        <div class="col-12 col-md-4 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.lastName" 
                                label="Last Name" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-4 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.firstName" 
                                label="First Name" 
                                stack-label 
                                dense
                            >
                            </q-input>
                        </div>
                        <div class="col-12 col-md-4 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.middleName" 
                                label="Middle Name" 
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
                            <q-input
                                outlined 
                                v-model="form.commission" 
                                label="Commission" 
                                stack-label 
                                dense
                            >
                            </q-input>
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
import createDocument from '../../firebase/firebase-create';

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
            form: {
                firstName: "",
                lastName: "",
                middleName: "",
                address: "",
                contact: "",
                commission: "",
                status: "active",
            },
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
                address: "",
                contact: "",
                commission: "",
                status: "active",
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
                createDocument('sellerList', payload);

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