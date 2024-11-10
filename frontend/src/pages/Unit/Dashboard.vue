<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <span class="text-h6 text-bold">Hi {{`${user.fullName}`}},</span><br/>
                <span class="text-caption">Welcome to ASCOT Scholarship Management System</span><br/>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <span class="text-h6 text-bold">Applications Subject for {{Number(user.userType) === 3 ? `Evaluation` : `Approval`}}</span><br/>
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
                    class="my-sticky-last-column-table"
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
                                        :id="`evaluatorProcess-${col.value.id}`"
                                        flat
                                        active-icon="ti-reload"
                                        active-color="orange"
                                        done-icon="mdi-check-all"
                                        done-color="green"
                                        class="customStepper"
                                    >
                                        <q-step
                                            class="no-content"
                                            header-nav
                                            :name="1"
                                            :done="Number(col.value.evaluatedBy) > 0"
                                        >
                                        </q-step>

                                        <q-step
                                            class="no-content"
                                            id="approverProcess"
                                            :name="2"
                                            icon="mdi-check-decagram"
                                            :done="Number(col.value.approvedBy) > 0"
                                        >
                                        </q-step>
                                    </q-stepper>
                                    <q-tooltip
                                        anchor="center middle" self="top middle"
                                        :target="`#evaluatorProcess-${col.value.id}`"
                                        class="bg-white text-black q-pa-md"
                                    >
                                        <span class="text-bold">Status:</span> {{props.row.status}} <br/>
                                        <span class="text-bold">Remarks:</span> {{props.row.remarks || 'No Remarks'}} <br/>
                                    </q-tooltip>
                                </div>
                                <div v-if="col.label !== 'Application Status'">
                                    {{ col.value }}
                                </div>
                            </q-td>
                            <q-td class="text-center">
                                <!-- For Approver -->
                                <!-- For Evaluator -->
                                <q-btn 
                                    v-if="Number(user.userType) === 3 && Number(props.row.data.appStatus) === 0"
                                    @click="viewApplication(props.row)"
                                    outline 
                                    rounded 
                                    size="sm"
                                    color="primary" 
                                    label="Evaluate" 
                                />
                                <q-btn 
                                    v-if="(Number(user.userType) === 3 || Number(user.userType) === 4) && Number(props.row.data.appStatus) !== 0"
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
            </div>
        </div>




        <!-- Drawer application preview -->
        <!-- Application Submit -->
        
        <q-drawer
            side="right"
            v-model="drawerRight"
            bordered
            overlay
            :width="900"
        >
            <q-scroll-area class="fit">
                <q-card
                    flat
                    class=" bg-white"
                >
                    <q-card-section class="row items-center no-wrap">
                        <div v-if="selectedProgram.data !== undefined">
                            <div class="text-h5 text-weight-bold">{{selectedProgram.studentName}}</div>
                        </div>
                        <q-space />
                        <q-btn size="sm" rounded color="red" icon="ti-close" label="Cancel" @click="drawerRight = !drawerRight" />
                    </q-card-section>
                    <q-separator />
                    <q-card-section >
                        <div v-if="selectedProgram.data !== undefined" class="row">
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Personal Information</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.civilStatus || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Civil Status</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${moment(selectedProgram.data.student.dateOfBirth).format("MMMM DD, YYYY") || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Date of Birth</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.contact || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Contact</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.email || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Email</span>
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Student Information</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.yrLvl || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Year Level</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${convertCourses(selectedProgram.data.student.courseId) || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Course</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.username || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Student Number</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.schoolAttended || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">School Attended</span>
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.schoolAddress || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">School Address</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                        
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Family Background</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.name || '--'} (${selectedProgram.data.familyBackground.father.livingStatus})`}}</span><br/>
                                <span class="text-caption text-grey">Fathers Name</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.occupation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Occupation</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.contact || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Contact</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.educAttainment || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Educational Attainement</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.name || '--'} (${selectedProgram.data.familyBackground.mother.livingStatus})`}}</span><br/>
                                <span class="text-caption text-grey">Fathers Name</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.occupation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Occupation</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.contact || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Contact</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.educAttainment || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Educational Attainement</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.totalIncome || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Parents Total Annual Income</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.noOfSiblings || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">No. of Siblings</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">If not living with parents: </span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.guardian.name || '--'} (${selectedProgram.data.familyBackground.mother.livingStatus})`}}</span><br/>
                                <span class="text-caption text-grey">Guardian Name</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.guardian.relation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Relationship</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.guardian.occupation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Occupation</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                            
                            <div class="col-12 col-md-12 q-pa-xs q-mt-md">
                                <span class="text-title text-bold">Requirement Checklist</span>
                            </div>
                            <div class="col-12 col-md-12">
                                <q-list>
                                    <q-item 
                                        v-for="(item, index) in selectedProgram.data.scholarship.requirements" 
                                        :key="item.name" 
                                        tag="label" 
                                        v-ripple
                                    >
                                        <q-item-section side>
                                            <q-icon :color="item.color" name="task_alt" />
                                        </q-item-section>

                                        <q-item-section>
                                            <q-item-label>{{ item.label }}</q-item-label>
                                        </q-item-section>

                                        <q-item-section side>
                                            <div v-if="item.fileUploaded === undefined">
                                                <q-btn outline size="sm" rounded color="red" label="Request For Upload" />
                                            </div>
                                            <div v-if="item.fileUploaded !== undefined">
                                                <q-btn
                                                    @click="previewDocs(item.uploadFile, item.name)"
                                                    outline 
                                                    size="sm" 
                                                    class="q-mr-xs" 
                                                    rounded 
                                                    color="primary" 
                                                    label="View"
                                                />
                                                <q-btn
                                                    v-if="item.verified === undefined"
                                                    @click="verifyDocument(item)"
                                                    outline size="sm" 
                                                    class="q-mr-xs" 
                                                    rounded color="green" 
                                                    label="Verify" 
                                                />
                                                <q-btn 
                                                    v-if="item.verified === undefined"
                                                    outline size="sm" 
                                                    rounded 
                                                    color="red" 
                                                    label="Request For Update" 
                                                />
                                            </div>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>

                            <div class="col-12 col-md-12 q-pa-xs q-mt-md">
                                <span class="text-title text-bold">Qualifications</span>
                            </div>
                            <div class="col-12 col-md-12">
                                <q-list>
                                    <q-item 
                                        v-for="(itm, indx) in selectedProgram.data.scholarship.qualification"
                                        :key="indx"
                                    >
                                        <q-item-section avatar>
                                            <q-avatar>
                                                <q-icon name="mdi-asterisk-circle-outline" size="sm" />
                                            </q-avatar>
                                        </q-item-section>

                                        <q-item-section>
                                            <q-item-label>
                                                {{itm.description}}
                                            </q-item-label>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-actions v-if="selectedProgram.data !== undefined">
                        <q-btn 
                            v-if="Number(user.userType) === 3 && Number(selectedProgram.data.evaluatedBy) === 0"
                            @click="updateApplicationData('evaluate')"
                            outline 
                            rounded 
                            size="md"
                            color="primary" 
                            label="Send for Approval" 
                        />
                        <q-btn 
                            v-if="Number(user.userType) === 4 && Number(selectedProgram.data.approvedBy) === 0"
                            @click="updateApplicationData('approve')"
                            outline 
                            rounded 
                            size="md"
                            color="primary" 
                            label="Approve Application" 
                        />
                    </q-card-actions>
                </q-card>
            </q-scroll-area>
        </q-drawer>


        <!-- Preview Modals -->
        <previewModal 
            :modalStatus="previewModalStatus"
            :appData="selectedFile"
            @updatePrintStatus="closeFormModal"
        />
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';
import previewModal from '../../components/Modals/PreviewDocument.vue';

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    components:{
        previewModal
    },
    data(){
        return {
            drawerRight: false,
            selectedProgram: {},
            tableLoading: false,
            itemsList: [],
            courseOpt: [],
            previewModalStatus: false,
            selectedFile: "",
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
            const user = LocalStorage.getItem('userData')
            return jwtDecode(user);
        },
        tableColumns: function(){
            return [
                {
                    name: 'sname',
                    required: true,
                    label: 'Student Number',
                    align: 'left',
                    field: row => row.studentNumber,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'name',
                    required: true,
                    label: 'Student Name',
                    align: 'left',
                    field: row => row.studentName,
                    format: val => `${val}`,
                    sortable: true
                },
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
                // { name: 'status', label: 'Status', field: 'status', align: 'left' },
            ]
        }
    },
    created(){
        this.getList()
        this.getCourses()
    },
    methods: {
        moment,
        async updateApplicationData(type){
            

            // Confirm
            this.$q.dialog({
                title: 'Update Application Status',
                message: 'Would you like to proceed with this transaction?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                this.$q.loading.show();
                let payload = {}

                if(type === "evaluate"){
                    payload = {
                        appId: Number(this.selectedProgram.data.id),
                        actionType: type,
                        updateDetails: {
                            appStatus: 1,
                            evaluatedBy: this.user.userId,
                            status: "Application has been moved for Approval",
                            dateEvaluated: moment().format("l LT"),
                        }
                    }
                } else {
                    payload = {
                        appId: Number(this.selectedProgram.data.id),
                        actionType: type,
                        updateDetails: {
                            appStatus: 2,
                            approvedBy: this.user.userId,
                            status: "Application has been Approved",
                            dateApproved: moment().format("l LT"),
                        }
                    }
                }


                this.$api.post('scholarship/update/application', payload).then(async (response) => {
                    const data = {...response.data};

                    if(!data.error){
                        this.$q.notify({
                            position: 'top-left',
                            type: 'success',
                            message: data.title,
                            caption: data.message,
                            icon: 'mdi-check-all'
                        })

                        this.getList()
                        this.drawerRight = false
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
            })
            
        },
        async verifyDocument(item){
            this.$q.loading.show();
            let payload = {
                fileId: item.fileId,
                updateDetails: {
                    status: 1,
                    remarks: "Evaluator verify and checked the Document is Valid",
                }
            }

            let index = this.selectedProgram.data.scholarship.requirements.indexOf(item)
            let requirements = this.selectedProgram.data.scholarship.requirements[index];

            
            this.$api.post('document/update/attachment', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    this.$q.notify({
                        position: 'top-left',
                        type: 'success',
                        message: data.title,
                        caption: data.message,
                        icon: 'mdi-check-all'
                    })

                    requirements.verified = true
                    requirements.color = 'blue-9'
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
        previewDocs(file, reqType){
            this.previewModalStatus = true
            this.selectedFile = {
                url: file,
                type: reqType
            }
        },
        closeFormModal(){
            this.previewModalStatus = false
        },
        viewApplication(val){
            this.drawerRight = true
            this.selectedProgram = val
        },
        checkStepProcess(data){
            let res = 1;

            if(Number(data.evaluatedBy) !== 0 && Number(data.approvedBy) === 0){
                res = 2
            } else if(Number(data.evaluatedBy) > 0 && Number(data.approvedBy) > 0){
                res = 3
            }
            return res
        },
        async getList(){
            this.tableLoading = true
            this.itemsList = []

            this.$api.post('scholarship/applied/list', {uType: Number(this.user.userType)}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsList = data.list
                }

                this.tableLoading = false
            })
        },
        getFileStatus(){
            let payload = {
                uid: this.selectedProgram.data.studentId,
            }

            this.$api.post('document/get/attachments', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    // fill the already uploaded document
                    data.list.forEach(el => {
                        let filterMatch = this.selectedProgram.data.scholarship.requirements.filter((elr) => {return elr.name === el.reqType})
                        let val = filterMatch[0]
                        let index = this.selectedProgram.data.scholarship.requirements.indexOf(val)
                        let requirements = this.selectedProgram.data.scholarship.requirements[index];

                        if(Number(el.status) !== 1){
                            requirements.fileId = Number(el.id)
                            requirements.file = el.fileName
                            requirements.fileUploaded = true
                            requirements.uploadFile = el.uploadFile
                            requirements.color = 'green'
                        } else {
                            requirements.fileId = Number(el.id)
                            requirements.file = el.fileName
                            requirements.fileUploaded = true
                            requirements.uploadFile = el.uploadFile
                            requirements.verified = true
                            requirements.color = 'blue-9'
                        }
                        
                    });
                } else {
                    // error
                }
            })

            this.loginLoad = false;
            this.$q.loading.hide();
        },
        convertCourses(val){
            const res = this.courseOpt.filter(el => el.value === val)
            return res[0].label 
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
