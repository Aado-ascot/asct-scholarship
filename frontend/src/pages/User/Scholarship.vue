<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        <span class="text-h6 text-bold">ASCT Scholarship Services</span><br/>
                        <div class="row">
                            <div
                            v-for="(item, idx) in services"
                            :key="idx"
                            class="col-12 col-md-4 q-pa-lg"
                            >
                            <q-card class="my-card" flat bordered>
                                <q-img src="/imgs/ASCOT-WEBSITE.png" />

                                <q-card-section>
                                <q-btn
                                    fab
                                    color="primary"
                                    :icon="item.icon"
                                    class="absolute"
                                    style="top: 0; right: 12px; transform: translateY(-50%);"
                                />

                                <div class="row no-wrap items-center">
                                    <div class="col text-h6 ellipsis">
                                    {{item.title}}
                                    </div>
                                </div>
                                </q-card-section>

                                <q-card-section class="q-pt-none">
                                <div class="text-caption text-grey">
                                    {{item.description}}
                                </div>
                                </q-card-section>

                                <q-separator />

                                <q-card-actions>
                                <q-btn @click="item.action" flat color="primary">
                                    {{item.buttonAction}}
                                </q-btn>
                                </q-card-actions>
                            </q-card>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>

        <printFormModal 
            :modalStatus="printModal"
            @updatePrintStatus="closeFormModal"
        />
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
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
            services: [
                {
                    title: "Available Scholarship",
                    description: "List of scholarship available and check for the requirements.",
                    icon: "ti-layout-list-thumb",
                    buttonAction: "Apply",
                    action: () => { this.openPrint() },
                },
                {
                    title: "Apply Scholarship",
                    description: "Check your eligibility for the scholarship program you want to apply",
                    icon: "mdi-book-edit-outline",
                    buttonAction: "Apply",
                    action: () => { this.openPrint() },
                },
                {
                    title: "Verification",
                    description: "Verification of the application and current status of your scholarship program",
                    icon: "mdi-check-decagram",
                    buttonAction: "Apply",
                    action: () => { this.openPrint() },
                },
            ]
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return profile;
        }
    },
    mounted(){
    },
    methods: {
        moment,
        openPrint(){
            this.printModal = true
        },
        closeFormModal(){
            this.printModal = false
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
