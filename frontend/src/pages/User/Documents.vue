<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm q-mb-sm">
                <span class="text-h6 text-bold">Documents</span><br/>
                <span class="text-subtitle1">Requirements for the application</span><br/>
                <span class="text-caption">Note: Only PDF Files is only allowed and have a maximum of 2MB</span><br/>
                <q-separator />
            </div>
            <div class="col-3 col-md-4">
                <q-tabs
                    v-model="requirementTab"
                    vertical
                    no-caps
                    inline-label
                    dense
                    active-color="primary"
                >
                    <q-tab
                        v-for="(tab, idx) in requirementTabs"
                        :key="idx"
                        :name="tab.name" 
                        :icon="tab.icon" 
                        :label="tab.label"
                        class="alignTextContent"
                        :class="`text-${tab.color}`"
                    />
                </q-tabs>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-8">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section class="fit row wrap justify-start items-center content-center">
                        <q-icon :name="reqStatus >= 1 ? 'mdi-check-decagram' : 'mdi-cog-transfer'" size="md" :color="reqStatus >= 1 ? 'green' : 'orange'" />
                        <span class="text-h5 text-bold q-ml-sm">{{selectedMenu.label}}</span>
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <span class="text-h6">Requirement Details</span>
                        <div class="row">
                            <div class="col-6 col-md-6 q-pa-sm">
                                <span class="text-caption">File Name</span> <br/>
                                <span class="text-bold">{{fileName || '--'}}</span>
                            </div>
                            <div class="col-6 col-md-6 q-pa-sm">
                                <span class="text-caption">Upload Date</span> <br/>
                                <span class="text-bold">{{currDate || '--'}}</span>
                            </div>
                            <div class="col-6 col-md-6 q-pa-sm">
                                <span class="text-caption">Requirement Status</span> <br/>
                                <span class="text-bold">{{reqStatus || '--'}}</span>
                            </div>
                            <div class="col-6 col-md-6 q-pa-sm">
                                <span class="text-caption">Remarks / Reccomendation</span> <br/>
                                <span class="text-bold">{{remarks || '--'}}</span>
                            </div>
                            <div v-if="!hasData" class="col-12 col-md-12 q-pa-sm">
                                <q-file 
                                    label="Upload your file here"
                                    bottom-slots 
                                    v-model="fileRacker"
                                    counter
                                    outlined
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="cloud_upload" @click.stop.prevent />
                                    </template>
                                    <template v-slot:append>
                                        <q-icon 
                                            v-if="fileRacker !== null" 
                                            name="mdi-trash-can"
                                            color="red"
                                            @click.stop.prevent="removeFile" 
                                            class="cursor-pointer" 
                                        />
                                    </template>
                                </q-file>
                            </div>
                        </div>
                    </q-card-section>
                    <q-separator />
                     <q-card-actions class="fit row wrap justify-end items-right content-center">
                        <!-- <q-icon name="mdi-check-decagram" size="md" color="green" /> -->
                        <!-- <q-icon name="mdi-cog-transfer" size="md" color="primary" /> -->
                        <q-btn
                            v-if="hasData"
                            label="Update Document" 
                            color="primary"
                            @click="submitData" 
                        />
                        <q-btn
                            v-if="!hasData"
                            label="Upload Document" 
                            color="primary"
                            @click="submitData" 
                        />
                    </q-card-actions>
                </q-card>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode'

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    data(){
        return {
            fileName: '',
            fileSize: '',
            uploadFile: '',
            remarks: '',
            currDate: dateNow,
            reqStatus: '',

            hasData: false,
            fileRacker: null,
            requirementTab: 'schoolCard',
            requirementTabs: [
                {
                    name: 'schoolCard',
                    icon: 'mdi-card-account-details',
                    label: 'Form 138/High School Card',
                    color: '',
                },
                {
                    name: 'lastCard',
                    icon: 'mdi-content-copy',
                    label: 'Copy of grades last semester attended (for old student)',
                    color: '',
                },
                {
                    name: 'goodMoral',
                    icon: 'mdi-certificate-outline',
                    label: 'Good Moral Character',
                    color: '',
                },
                {
                    name: 'psa',
                    icon: 'mdi-check-decagram',
                    label: 'Birth Certificate/PSA',
                    color: '',
                },
                {
                    name: 'regForm',
                    icon: 'mdi-form-select',
                    label: 'Registration Form/Proof of enrollment',
                    color: '',
                },
                {
                    name: 'entranceTest',
                    icon: 'mdi-file-sign',
                    label: 'Entrance Test Result (for new student)',
                    color: '',
                },
                {
                    name: 'indigency',
                    icon: 'mdi-file-certificate',
                    label: 'Certificate of Indigency/Income Tax Return of Parents',
                    color: '',
                }
            ]
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        },
        selectedMenu(){
            let menus = [...this.requirementTabs]
            this.getFileStatus();
            let selected = menus.filter(el => { return el.name === this.requirementTab })
            return selected[0]
        }
    },
    watch: {
        fileRacker: async function(newVal){
            let convertedFile = await this.getBase64(newVal)
            this.fileName = newVal.name
            this.fileSize = newVal.size
            this.uploadFile = convertedFile
        }
    },
    methods: {
        moment,
        getFileStatus(){
            this.$q.loading.show();
            let payload = {
                uid: this.user.userId,
                type: this.requirementTab
            }

            this.$api.post('document/get/attachment', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    this.fileName = data.fileName
                    this.fileSize = data.fileSize
                    this.uploadFile = data.uploadFile
                    this.remarks = data.remarks
                    this.currDate = data.createdDate
                    this.reqStatus = data.status
                    this.hasData = true
                } else {
                    this.fileName = ''
                    this.fileSize = ''
                    this.uploadFile = ''
                    this.remarks = ''
                    this.currDate = dateNow
                    this.reqStatus = ''
                    this.hasData = false
                }
            })

            this.loginLoad = false;
            this.$q.loading.hide();
        },
        removeFile(){
            this.fileRacker = null;
            this.fileName = ''
            this.fileSize = ''
        },
        async getBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        },
        async submitData() {
            this.$q.loading.show();
            let payload = {
                userId: this.user.userId,
                reqType: this.requirementTab,
                fileName: this.fileName,
                fileSize: this.fileSize,
                uploadFile: this.uploadFile,
                remarks: 'Waiting for validation of the Registrar',
                status: 0
            }

            this.$api.post('document/upload', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    this.$q.notify({
                        position: 'top-left',
                        type: 'success',
                        message: data.title,
                        caption: data.message,
                        icon: 'mdi-check-all'
                    })
                } else {
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'mdi-alert-circle-outline'
                    })
                }
            })

            this.loginLoad = false;
            this.$q.loading.hide();
        },
    }
}
</script>
<style scoped>
.alignTextContent {
    display: flex;
    align-content: flex-start !important;
    justify-content: flex-start;
    align-items: self-start;
    text-align: left !important;
}
.my-card{
    border-radius: 1px;
    box-shadow: 0px 0px 3px -2px !important;
}
.my-card-item{
    border-radius: 10px;
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
