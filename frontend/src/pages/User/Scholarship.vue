<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-card
                    flat
                    class="bg-white"
                >
                    <q-card-section>
                        My Application List
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
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
                            flat
                            bordered
                            :rows="itemsList"
                            wrap-cells
                            :columns="tableColumns"
                            row-key="name"
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
                                    <q-th class="text-center">
                                        Actions
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
                                        <div v-if="col.label === 'Application Status'" class="text-center">
                                            <q-stepper
                                                :model-value="checkStepProcess(col.value)"
                                                ref="stepper"
                                                contracted
                                                color="orange"
                                                flat
                                                :id="`evaluatorProcess-${col.value.id}`"
                                                active-icon="mdi-cog-clockwise"
                                                active-color="orange"
                                                done-icon="mdi-check-all"
                                                done-color="green"
                                                error-icon="mdi-close-circle"
                                                class="customStepper"
                                            >
                                                <q-step
                                                    class="no-content"
                                                    :name="0"
                                                    :done="true"
                                                >
                                                    
                                                </q-step>

                                                <q-step
                                                    class="no-content"
                                                    :name="1"
                                                    :error="Number(col.value.appStatus) === 3"
                                                    :done="Number(col.value.evaluatedBy) > 0 && Number(col.value.appstatus) !== 3"
                                                >  
                                                </q-step>

                                                <q-step
                                                    class="no-content"
                                                    :name="2"
                                                    title="Create an ad group"
                                                    icon="mdi-check-decagram"
                                                    :error="Number(col.value.appStatus) === 3"
                                                    :done="Number(col.value.approvedBy) > 0 && Number(col.value.appstatus) !== 3"
                                                >
                                                </q-step>
                                            </q-stepper>
                                            <q-tooltip
                                                anchor="center middle" self="top middle"
                                                :target="`#evaluatorProcess-${col.value.id}`"
                                                class="bg-white text-black q-pa-md"
                                            >
                                                <span class="text-bold">Submit Application:</span> Done <br/>
                                                <span class="text-bold">Evaluate:</span> -- <br/>
                                                <span class="text-bold">Approve:</span> -- <br/>
                                                <span class="text-bold">Remarks:</span> {{props.row.status || 'No Remarks'}} <br/>
                                            </q-tooltip>
                                        </div>
                                        <div v-if="col.label !== 'Application Status'">
                                            {{ col.value }}
                                        </div>
                                    </q-td>
                                    <q-td class="text-center">
                                        <q-btn 
                                            @click="viewApplication(props.row)"
                                            outline 
                                            rounded 
                                            size="sm"
                                            color="secondary" 
                                            label="View Details" 
                                        />
                                    </q-td>
                                </q-tr>
                            </template>
                        </q-table>
                    </q-card-section>
                </q-card>
            </div>
        </div>


        <!-- Application Submit -->
        
        <!-- End of Application Submit Drawer -->
        <printFormModal 
            :modalStatus="printModal"
            :appData="appData"
            @updatePrintStatus="closeFormModal"
        />
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';
import printFormModal from '../../components/Modals/PrintFormModel.vue';


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    components: {
        printFormModal
    },
    data(){
        return {
            step: 1,
            printModal: false,
            appData: {},
            itemsList: [],

            drawerRight: false,
            selectedProgram: {},
            myInfo: {},
            courseOpt: [],
            requirementTab: "",

            form: {
                father:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                mother:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                totalIncome: 0,
                noOfSiblings: 0,
                notWithParents: true,
                guardian: {
                    name:"",
                    occupation:"",
                    relation:"",
                },
            },
        }
    },
    watch:{
        drawerRight(newVal){
            if(newVal){
                this.getFileStatus()
            }
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        },
        tableColumns: function(){
            return [
                {
                    name: 'title',
                    required: true,
                    label: 'Scholarship Applied',
                    align: 'left',
                    field: row => row.title,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'provider', label: 'Type', field: 'provider', align: 'left' },
                { name: 'processFlow', label: 'Application Status', field: 'data', align: 'center',},
                // { name: 'remarks', label: 'Remarks', field: 'remarks', align: 'left' },
            ]
        }
    },
    created(){
        this.getCourses()
        this.getApplied()
        this.getMyDetails()
    },
    methods: {
        moment,
        async getApplied(){
            this.tableLoading = true
            this.itemsList = []

            this.$api.post('scholarship/applied/status', {sId: this.user.userId}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsList = data.list

                    let checkApproved = data.list.filter(el => {return Number(el.data.appStatus) === 2})
                    this.approvedApplication = checkApproved.length > 0 ? checkApproved[0] : null
                } else {
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'report_problem'
                    })
                }

                this.tableLoading = false
            })
        },
        checkStepProcess(data){
            let res = 1;

            if(Number(data.evaluatedBy) !== 0 && Number(data.approvedBy) === 0){
                res = 2
            } else if(Number(data.evaluatedBy) > 0 && Number(data.approvedBy) > 0){
                res = 3
            } else if(Number(data.rejectedBy) > 0){
                res = 4
            }
            return res
        },
        displayStatus(data){
            let res = "";

            if(Number(data.evaluatedBy) !== 0 && Number(data.approvedBy) === 0){
                res = ""
            } else if(Number(data.evaluatedBy) > 0 && Number(data.approvedBy) > 0){
                res = ""
            } else if(Number(data.rejectedBy) > 0){
                res = "Rejected Application"
            }
            return res
        },
        async submitApplicationFormData(){
            this.$q.loading.show();
            this.loginLoad = true;
            let vm = this;
            let payload = {
                status: "Submitted application for evaluation",
                familyBackground: this.form,
                studentId: Number(this.user.userId),
                scholarId: Number(this.selectedProgram.id),
            };

            this.$api.post('scholarship/submit/application', payload).then(async (response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$q.notify({
                        position: 'top-left',
                        type: 'positive',
                        message: data.title,
                        caption: data.message,
                        icon: 'verified'
                    })
                    this.resetForm()
                    this.drawerRight = false
                } else {
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'report_problem'
                    })
                }
            })

            this.$q.loading.hide();
        },
        resetForm(){
            this.form = {
                father:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                mother:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                totalIncome: 0,
                noOfSiblings: 0,
                notWithParents: true,
                guardian: {
                    name:"",
                    occupation:"",
                    relation:"",
                },
            }
        },
        async checkFile(fileVal, index){
            this.$q.loading.show();
            let convertedFile = await this.getBase64(fileVal)
            let payload = {
                userId: this.user.userId,
                reqType: this.selectedProgram.requirements[index].name,
                fileName: fileVal.name,
                fileSize: fileVal.size,
                uploadFile: convertedFile,
                remarks: 'Waiting for validation of the scholarship unit',
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
                    this.selectedProgram.requirements[index].fileUploaded = true
                    this.selectedProgram.requirements[index].color = 'green'
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

            this.$q.loading.hide();
        },
        async getBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        },
        getFileStatus(){
            let payload = {
                uid: this.user.userId,
            }

            this.$api.post('document/get/attachments', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    // fill the already uploaded document
                    data.list.forEach(el => {
                        let filterMatch = this.selectedProgram.requirements.filter((elr) => {return elr.name === el.reqType})
                        let val = filterMatch[0]
                        let index = this.selectedProgram.requirements.indexOf(val)

                        this.selectedProgram.requirements[index].file = el.fileName
                        this.selectedProgram.requirements[index].uploadFile = el.uploadFile
                        this.selectedProgram.requirements[index].fileUploaded = true
                        this.selectedProgram.requirements[index].color = 'green'
                    });
                } else {
                    // error
                }
            })

            this.loginLoad = false;
            this.$q.loading.hide();
        },
        applyForScholarship(val){
            this.drawerRight = true
            this.selectedProgram = val
        },
        openPrint(){
            let data = {
                student: {...this.myInfo},
                scholar: {...this.selectedProgram},
                others: {...this.form}
            }
            this.appData = data
            this.printModal = true
        },
        closeFormModal(){
            this.printModal = false
        },
        async getList(){
            this.itemsList = []
            this.$api.get('scholarship/list').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsList = data.list
                } else {
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'report_problem'
                    })
                }
            })
        },
        async getMyDetails(){
            this.$api.post('users/getUserById', {id:this.user.userId}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.myInfo = data
                } else {
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'report_problem'
                    })
                }
            })
        },
        convertCourses(courseArray){
            let result = ""
            let list = []

            if(courseArray === undefined){
                return "--"
            }

            
            courseArray = courseArray.split(",")
            list = courseArray.map((val, indx) => {
                const res = this.courseOpt.filter(el => el.value === val)
                return res[0].label
            });
            // console.log(list)
            // result = list.join(" , ")

            return list 
        },
        async getCourses(){
            this.courseOpt = [];
            this.$api.get('misc/courseList').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    let opt = data.list.map((el) => {
                        return {
                            label: el.title,
                            value: el.id
                        }
                    })
                    
                    this.courseOpt = opt
                } else {
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'report_problem'
                    })
                }
            })
        },
    }
}
</script>
<style scoped>
.customStepper{
    
    padding: 0px !important;
}
.itemBorder{
    border-left: 3px solid rgb(0,177,250);
}
.q-stepper__header--contracted {
    min-height: 32px!important;
}
.no-content{
    display: none;
}
.my-card{
    border-radius: 15px;
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
