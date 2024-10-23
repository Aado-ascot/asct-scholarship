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
                            <div class="text-h5 text-weight-bold">{{this.selectedProgram.title}}</div>
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
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.lastName || '--'} ${myInfo.firstName || '--'} ${myInfo.suffix || '--'} ${myInfo.middleName || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Name</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${myInfo.yrLvl || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Year Level</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${moment(myInfo.dateOfBirth).format("MMMM DD, YYYY") || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey"></span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.slot || '0'}`}}</span><br/>
                                        <span class="text-caption text-grey">Available Slot</span>
                                    </div>
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">{{`${convertCourses(form.coveredCourses) || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Courses Covered on Program</span>
                                    </div>
                                </div> 
                                <q-separator />
                                <div class="row">
                                    
                                </div>
                            </q-step>

                            <q-step
                                :name="2"
                                title="Requirements"
                                icon="create_new_folder"
                                :done="step > 2"
                            >
                                Upload the Requirements
                                <div class="row">
                                    
                                </div>
                            </q-step>

                            <q-step
                                :name="3"
                                title="Summary"
                                icon="assignment"
                            >
                                Summary of the Application and Preview the Application Form
                                <!-- <div class="row">
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.title || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Scholarship Program Title</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.provider.label || '0'}`}}</span><br/>
                                        <span class="text-caption text-grey">Scholarship Provider</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${moment(form.dueDate).format("MMMM DD, YYYY") || '0'}`}}</span><br/>
                                        <span class="text-caption text-grey">Valid Until</span>
                                    </div>
                                    <div class="col-4 q-mb-sm">
                                        <span class="text-title text-bold">{{`${form.slot || '0'}`}}</span><br/>
                                        <span class="text-caption text-grey">Available Slot</span>
                                    </div>
                                    <div class="col-12 q-mb-sm">
                                        <span class="text-title text-bold">{{`${convertCourses(form.coveredCourses) || '--'}`}}</span><br/>
                                        <span class="text-caption text-grey">Courses Covered on Program</span>
                                    </div>
                                </div> -->
                            </q-step>
                        </q-stepper>

                        
                    </q-card-section>
                    <q-separator />
                    <q-card-actions>
                        <q-stepper-navigation >
                            <q-btn v-if="step < 3" @click="$refs.stepper.next()" color="primary" label="Next" />
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
            printModal: false,
            itemsList: [],

            drawerRight: false,
            selectedProgram: {},
            myInfo: {},
            courseOpt: [],
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        }
    },
    created(){
        this.getList()
        this.getMyDetails()
    },
    methods: {
        moment,
        applyForScholarship(val){
            this.drawerRight = true
            this.selectedProgram = val
        },
        openPrint(){
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
