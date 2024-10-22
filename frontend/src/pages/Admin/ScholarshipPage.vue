<template>
    <div class="q-pa-sm" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12  q-pa-sm">
                <q-card
                    flat
                    class="bg-white"
                >
                    <q-card-section class="fit row wrap justify-start items-center content-center">
                        <span class="text-h6 text-bold">
                            Scholarship Management
                            <br/>
                            <span class="text-caption text-grey">Manage user details and access</span>
                        </span>
                        
                        <q-space />
                        <div class="text-right">
                            <q-btn-group>
                                <!-- <q-btn color="amber" rounded glossy icon="visibility" /> -->
                                <q-btn color="primary" rounded  icon="ti-plus" label="Add New Program" no-caps @click="drawerRight = !drawerRight" />
                            </q-btn-group>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
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
                            :rows="itemsList"
                            wrap-cells
                            :columns="columns"
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
                                    <q-th>
                                        Action
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
                                        {{ col.value }}
                                    </q-td>
                                    <q-td class="text-center">
                                        <q-btn
                                            @click="showPayroll(props.row)"
                                            flat 
                                            round 
                                            color="primary" 
                                            size="md" 
                                            icon="mdi-calculator-variant" 
                                        />
                                    </q-td>

                                </q-tr>
                            </template>
                        </q-table>
                    </q-card-section>
                </q-card>
            </div>

        </div>

        <!-- Modals -->
        <q-drawer
            side="right"
            v-model="drawerRight"
            bordered
            overlay
            :width="700"
        >
            <q-scroll-area class="fit">
                <q-card
                    flat
                    class=" bg-white"
                >
                    <q-card-section class="row items-center no-wrap">
                        <div>
                            <div class="text-weight-bold">Create Scholarship Program</div>
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
                                    <div class="col-12 col-md-12 text-grey-8 q-pa-xs">
                                        <q-input
                                            v-model="form.title"
                                            label="Title"
                                            placeholder="Enter Scholarship Program"
                                            outlined
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                        <q-input
                                            v-model="form.otherDetailsLink"
                                            label="Link for more Details"
                                            placeholder="Enter URL Link for the other information"
                                            outlined
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
                                        <q-input
                                            v-model="form.slot"
                                            label="Available Slot"
                                            placeholder="Enter Slots"
                                            outlined
                                            stack-label
                                        >
                                        </q-input>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-pa-xs">
                                        <q-select
                                            outlined
                                            stack-label 
                                            v-model="form.coveredCourses"
                                            :options="courseOpt"
                                            label="Course Covered"
                                            placeholder="Select course covered on this Program"
                                            multiple
                                            emit-value
                                            map-options
                                        >
                                            <template v-slot:option="{ itemProps, opt, selected, toggleOption }">
                                                <q-item v-bind="itemProps">
                                                    <q-item-section>
                                                        <q-item-label v-html="opt.label" />
                                                    </q-item-section>
                                                    <q-item-section side>
                                                        <q-checkbox :model-value="selected" checked-icon="task_alt" unchecked-icon="radio_button_unchecked" @update:model-value="toggleOption(opt)" />
                                                    </q-item-section>
                                                </q-item>
                                            </template>
                                        </q-select>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div class="fit row wrap justify-start items-center content-center">
                                            <div class="text-bold text-h6 q-mb-sm">Qualifications</div>
                                            <q-space />
                                            <div class="text-right">
                                                <q-btn-group>
                                                    <q-btn color="secondary" rounded  icon="ti-plus" label="Add Qualification" no-caps size="sm" @click="addQualificationItem" />
                                                </q-btn-group>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div v-for="(itm, idx) in form.qualification" :key="idx" class="row">
                                            <div class="col-12 col-md-11 q-pa-sm">
                                                <q-input
                                                    outlined
                                                    v-model="itm.targetValue" 
                                                    label="Target Qualification"
                                                    stack-label 
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            <div class="col-12 col-md-1 q-pa-sm text-right">
                                                <q-btn @click="removeItem(idx)" size="sm" class=" q-mt-xs" color="red" icon="ti-trash" />
                                            </div>
                                            <div class="col-12 col-md-12 q-pa-sm">
                                                <q-input
                                                    type="textarea"
                                                    outlined
                                                    v-model="itm.description" 
                                                    label="Description" 
                                                    stack-label
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            
                                            
                                        </div>
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
                                    <div 
                                        v-for="(req, index) in requirementLst"
                                        :key="index"
                                        class="col-12 col-md-12 q-mt-sm q-pa-xs"
                                    >
                                        <q-checkbox
                                            v-model="req.required"
                                            checked-icon="task_alt" 
                                            unchecked-icon="radio_button_unchecked" 
                                        >
                                            {{req.label}}
                                        </q-checkbox>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div class="fit row wrap justify-start items-center content-center">
                                            <div class="text-bold text-h6 q-mb-sm">Other Requirements</div>
                                            <q-space />
                                            <div class="text-right">
                                                <q-btn-group>
                                                    <q-btn color="secondary" rounded  icon="ti-plus" label="Add Qualification" no-caps size="sm" @click="addQualificationItem" />
                                                </q-btn-group>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 text-grey-8 q-mt-sm q-pa-xs">
                                        <div v-for="(itm, idx) in form.otherRequirements" :key="idx" class="row">
                                            <div class="col-12 col-md-11 q-pa-sm">
                                                <q-input
                                                    outlined
                                                    v-model="itm.targetValue" 
                                                    label="Target Qualification"
                                                    stack-label 
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            <div class="col-12 col-md-1 q-pa-sm text-right">
                                                <q-btn @click="removeItem(idx)" size="sm" class=" q-mt-xs" color="red" icon="ti-trash" />
                                            </div>
                                            <div class="col-12 col-md-12 q-pa-sm">
                                                <q-input
                                                    type="textarea"
                                                    outlined
                                                    v-model="itm.description" 
                                                    label="Description" 
                                                    stack-label
                                                    dense
                                                >
                                                </q-input>
                                            </div>
                                            
                                            
                                        </div>
                                    </div>
                                </div>
                            </q-step>

                            <q-step
                                :name="3"
                                title="Summary"
                                icon="assignment"
                            >
                                This step won't show up because it is disabled.
                            </q-step>

                            
                            
                            <template v-slot:navigation>
                                
                            </template>
                        </q-stepper>

                        
                    </q-card-section>
                    <q-separator />
                    <q-card-actions>
                        <q-stepper-navigation >
                            <q-btn @click="$refs.stepper.next()" color="primary" :label="step === 3 ? 'Finish' : 'Next'" />
                            <q-btn v-if="step > 1" flat color="primary" @click="$refs.stepper.previous()" label="Back" class="q-ml-sm" />
                        </q-stepper-navigation>
                    </q-card-actions>
                </q-card>
            </q-scroll-area>
        </q-drawer>
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
// import addEmployeeModal from "../../components/Modals/AddEmployeeModal.vue"

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'PayrollPage',
    components:{
        // addEmployeeModal
    },
    data(){
        return {
            itemsList: [],
            drawerRight: false,
            step: 1,
            courseOpt: [],
            form: {
                title: "",
                slot: "",
                otherDetailsLink: "",
                qualification: [],
                coveredCourses: [],
                requirements: [],
                createdBy: 0,
                dueDate: "",
                status: 1,
            },
            otherRequirements: [],
            requirementLst: [
                {
                    name: 'schoolCard',
                    label: 'Form 138/High School Card',
                    required: false,
                },
                {
                    name: 'lastCard',
                    label: 'Copy of grades last semester attended (for old student)',
                    required: false,
                },
                {
                    name: 'goodMoral',
                    label: 'Good Moral Character',
                    required: false,
                },
                {
                    name: 'psa',
                    label: 'Birth Certificate/PSA',
                    required: false,
                },
                {
                    name: 'regForm',
                    label: 'Registration Form/Proof of enrollment',
                    required: false,
                },
                {
                    name: 'entranceTest',
                    label: 'Entrance Test Result (for new student)',
                    required: false,
                },
                {
                    name: 'indigency',
                    label: 'Certificate of Indigency/Income Tax Return of Parents',
                    required: false,
                },
                {
                    name: 'picture',
                    label: '2X2 Picture With Clear Details',
                    required: false,
                },
            ]
        }
    },
    computed: {
        columns(){
            return [
                {
                    name: 'name',
                    required: true,
                    label: 'Name',
                    align: 'left',
                    field: row => row,
                    format: val => `${val.firstName} ${val.lastName}`,
                    sortable: true
                },
                { name: 'startDate', align: 'left', label: 'Hired Date', field: 'startDate' },
                { name: 'status', align: 'left', label: 'End Date', field: 'status', sortable: true },
                {
                    name: 'contact',
                    required: true,
                    label: 'Contact Number',
                    align: 'left',
                    field: row => row.contact,
                    format: val => `${val}`,
                    sortable: true
                },
                
            ]
        },
        
    },
    created(){
        this.getCourses()
    },
    methods: {
        moment,
        addQualificationItem(){
            this.form.qualification.push({
                description: "",
                targetValue: "",
            })
        },
        removeItem(index){
            this.form.qualification.splice(index, 1)
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
        }
    }
}
</script>
<style scoped>
.my-card{
    border-radius: 10px;
    box-shadow: 0px 0px 3px -2px !important;
}
.my-card-item{
    border-radius: 5px;
    border: 1px solid gray;
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
.prod-item{
    cursor: pointer;
    height: 1.5rem;
    border-radius: 4px;
}
.prod-item:hover{
    background-color: rgb(199, 196, 196);
}
</style>
