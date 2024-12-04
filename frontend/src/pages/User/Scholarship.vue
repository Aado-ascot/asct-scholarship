<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        <!-- <span class="text-h6 text-bold">ASCT Scholarship Services</span><br/> -->
                        <div class="row">
                            <div
                            v-for="(item, idx) in itemsList"
                            :key="idx"
                            class="col-12 col-md-4 "
                            >
                            <q-card class="my-card" flat bordered>
                                <q-img :src="`/imgs/${item.original.provider}-WEBSITE.png`" />
                
                                <q-card-section>
                                    <q-btn
                                        fab
                                        color="primary"
                                        icon="mdi-calendar-multiple"
                                        class="absolute"
                                        style="top: 0; right: 12px; transform: translateY(-50%);"
                                    />

                                    <div class="row no-wrap items-center">
                                        <div class="col text-h6 ellipsis">
                                            {{item.title}}
                                        </div>
                                    </div>
                                    <div class="row no-wrap items-center">
                                        <div class="col text-caption">
                                            Submission Due Date: {{item.original.dueDate}}
                                        </div>
                                    </div>
                                </q-card-section>

                                <q-card-section class="q-pt-none">
                                    <q-list>
                                        <q-item-label class="text-bold" header>Qualifications</q-item-label>
                                        <q-item 
                                            v-for="(itm, indx) in item.original.qualification"
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
                                </q-card-section>

                                <q-separator />

                                <q-card-actions>
                                    <q-btn @click="applyForScholarship(item.original)" flat color="primary">
                                        Submit Application
                                    </q-btn>
                                </q-card-actions>
                            </q-card>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>


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
                        <div>
                            <div class="text-h5 text-weight-bold">{{selectedProgram.title}}</div>
                        </div>
                        <q-space />
                        <q-btn size="sm" rounded color="red" icon="ti-close" label="Cancel" @click="drawerRight = !drawerRight" />
                    </q-card-section>
                    <q-separator />
                    <q-card-section >
                        <q-stepper
                            v-model="step"
                            ref="stepper"
                            color="primary"
                            animated
                            flat
                            :swipeable="false"
                        >
                            <q-step
                                :name="1"
                                title="Details"
                                icon="settings"
                                :done="step > 1"
                            >   
                                <div class="row">
                                    <div class="col-6 q-mb-sm">
                                        <span class="text-caption text-grey">Available Slot: </span>
                                        <span class="text-title text-bold">{{`${Number(selectedProgram.slot) - Number(selectedProgram.applied) || '0'}/${selectedProgram.slot || '0'}`}}</span>
                                    </div>
                                    <div class="col-6 q-mb-sm">
                                        <span class="text-caption text-grey">Read more details for this program: </span>
                                        <span class="text-title text-bold"><a :href="selectedProgram.otherDetailsLink" target="_blank"> {{`${selectedProgram.otherDetailsLink || '--'}`}}</a></span>
                                    </div>
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-caption text-grey">Courses Covered on Program: </span><br/>
                                        <q-chip 
                                        v-for="(itm, indx) in convertCourses(selectedProgram.coveredCourses)"
                                        :key="indx"
                                        outline color="primary" text-color="white">
                                            {{ itm }}
                                        </q-chip>
                                    </div>
                                </div> 
                                <q-separator />
                                <div class="row q-mt-md">
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Father Details</span>
                                    </div>
                                    <div class="col-12 col-md-8 q-pa-xs">
                                        <q-input
                                            v-model="form.father.name"
                                            label="Father's Name"
                                            placeholder="Enter Name"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <div class="q-gutter-sm">
                                            <q-radio v-model="form.father.livingStatus" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" val="living" label="Living" />
                                            <q-radio v-model="form.father.livingStatus" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" val="deceased" label="Deceased" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.father.occupation"
                                            label="Occupation"
                                            placeholder="Enter occupation"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.father.educAttainment"
                                            label="Educational Attainment"
                                            placeholder="Enter educational attainment"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.father.contact"
                                            label="Contact Number"
                                            placeholder="Enter Mobile/Phone"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <q-input
                                            type="textarea"
                                            v-model="form.father.address"
                                            label="Address"
                                            placeholder="Enter Address"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Mother Details</span>
                                    </div>
                                    <div class="col-12 col-md-8 q-pa-xs">
                                        <q-input
                                            v-model="form.mother.name"
                                            label="Mothers's Name"
                                            placeholder="Enter Name"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <div class="q-gutter-sm">
                                            <q-radio v-model="form.mother.livingStatus" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" val="living" label="Living" />
                                            <q-radio v-model="form.mother.livingStatus" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" val="deceased" label="Deceased" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.mother.occupation"
                                            label="Occupation"
                                            placeholder="Enter occupation"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.mother.educAttainment"
                                            label="Educational Attainment"
                                            placeholder="Enter educational attainment"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.mother.contact"
                                            label="Contact Number"
                                            placeholder="Enter Mobile/Phone"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <q-input
                                            type="textarea"
                                            v-model="form.mother.address"
                                            label="Address"
                                            placeholder="Enter Address"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Other Details</span>
                                    </div>
                                    <div class="col-12 col-md-6 q-pa-xs">
                                        <q-input
                                            v-model="form.totalIncome"
                                            label="Total Parent Gross Income"
                                            placeholder="Enter Amount"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-6 q-pa-xs">
                                        <q-input
                                            v-model="form.noOfSiblings"
                                            label="No. Of Siblings in the family"
                                            placeholder="Enter Count"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <span class="text-title">Are you living with your parents?: </span>
                                        <div class="q-gutter-sm">
                                            <q-radio v-model="form.notWithParents" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" :val="true" label="Yes" />
                                            <q-radio v-model="form.notWithParents" checked-icon="task_alt" unchecked-icon="panorama_fish_eye" :val="false" label="No" />
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!form.notWithParents" class="row">
                                    <div class="col-12 col-md-12 q-pa-xs">
                                        <span class="text-title text-bold">Guardian Details</span>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.guardian.name"
                                            label="Guardian Name"
                                            placeholder="Enter name"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.guardian.occupation"
                                            label="Occupation"
                                            placeholder="Enter value"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-4 q-pa-xs">
                                        <q-input
                                            v-model="form.guardian.relation"
                                            label="Relationship"
                                            placeholder="Enter relation to the guadian"
                                            outlined
                                            dense
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    
                                </div>


                            </q-step>

                            <q-step
                                :name="2"
                                title="Requirements"
                                icon="create_new_folder"
                                :done="step > 2"
                            >
                                <div class="row">
                                    <div class="col-12 col-md-12">
                                        <q-list>
                                            <q-item 
                                                v-for="(item, index) in selectedProgram.requirements" 
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
                                                    <span v-if="item.fileUploaded !== undefined">Already uploaded</span>
                                                    <q-file
                                                        v-if="item.fileUploaded === undefined"
                                                        outlined  
                                                        v-model="item.file"  
                                                        label="Upload File Here"
                                                        @update:model-value="evnt => {return checkFile(evnt, index)}"
                                                        dense
                                                    >
                                                        <template v-slot:prepend>
                                                        <q-icon name="cloud_upload" @click.stop.prevent />
                                                        </template>
                                                        <template v-slot:append>
                                                        <q-icon name="close" @click.stop.prevent="item.file = null" class="cursor-pointer" />
                                                        </template>
                                                    </q-file>
                                                </q-item-section>
                                            </q-item>
                                        </q-list>
                                    </div>
                                </div>
                            </q-step>

                            <q-step
                                :name="3"
                                title="Summary"
                                icon="assignment"
                            >
                                <div class="row">
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.lastName || '--'} ${myInfo.firstName || '--'} ${myInfo.suffix || ''} ${myInfo.middleName || ''}`}}</span><br/>
                                        <span class="text-caption text-grey">Name</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.yrLvl || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Year Level</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${moment(myInfo.dateOfBirth).format("MMMM DD, YYYY") || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Date of Birth</span>
                                    </div>
                                </div>
                            </q-step>
                        </q-stepper>

                        
                    </q-card-section>
                    <q-separator />
                    <q-card-actions>
                        <q-stepper-navigation >
                            <q-btn v-if="step < 3" @click="$refs.stepper.next()" color="primary" label="Continue" />
                            <q-btn v-if="step === 3" @click="openPrint" class="q-mr-sm" color="positive" label="Preview Form" />
                            <q-btn v-if="step === 3" @click="submitApplicationFormData" color="positive" label="Finish" />
                            <q-btn v-if="step > 1" flat color="primary" @click="$refs.stepper.previous()" label="Back" class="q-ml-sm" />
                        </q-stepper-navigation>
                    </q-card-actions>
                </q-card>
            </q-scroll-area>
        </q-drawer>
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
            }
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
        }
    },
    created(){
        this.getCourses()
        this.getList()
        this.getMyDetails()
    },
    methods: {
        moment,
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
