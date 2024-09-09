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
                        <span class="text-h6 text-bold">Dashboard Check Overview</span><br/>
                        <span class="text-caption text-grey">Invoice & Check summary</span><br/>

                        <div class="row">
                            <div
                                v-for="(item, idx) in dashCards"
                                :key="idx"
                                class="col-3 col-xs-12 col-sm-4 col-md-3 q-pa-xs"
                            >
                                <q-card
                                    flat
                                    class="my-card-item "
                                    :class="item.color"
                                >

                                    <q-card-section>
                                        <q-avatar
                                            size="md"
                                            :color="item.iconBg"
                                            text-color="white"
                                            :icon="item.icon"
                                        />
                                        <br/>
                                        <span class="text-bold text-h6 text-blue-grey-9" >{{item.value}}</span>
                                        <br/>
                                        <span class="text-subtitle text-blue-grey-9" >{{item.title}}</span>
                                        <br/>
                                        <span class="text-caption ellipsis" :class="item.captionColor" >{{item.captions}}</span>
                                    </q-card-section>
                                </q-card>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>



        <!-- Modals -->

    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    data(){
        return {
            dashCards: [
              {
                  key: 'applicants',
                  title: 'New Check Voucher',
                  value: 0,
                  subVal: '',
                  type: '',
                  info: '',
                  icon: 'contact_emergency',
                  valueType: 'Count',
                  chartType: '',
                  color: 'bg-amber-2',
                  iconBg: 'amber-5',
                  captions: 'All check vouchers recorded and realeased',
                  captionColor: 'text-blue-14',
              },
              {
                  key: 'totalLoans',
                  title: 'For Entry',
                  value: 0,
                  subVal: '',
                  type: '',
                  info: '',
                  icon: 'devices_other',
                  valueType: 'Count',
                  chartType: '',
                  color: 'bg-light-green-2',
                  iconBg: 'light-green-5',
                  captions: 'Checks for Entry to be release',
                  captionColor: 'text-blue-14',
              },
              {
                  key: 'totalRedeem',
                  title: 'Cancelled',
                  value: 0,
                  subVal: '',
                  type: '',
                  info: '',
                  icon: 'fact_check',
                  valueType: 'Count',
                  chartType: '',
                  color: 'bg-blue-grey-2',
                  iconBg: 'blue-grey-5',
                  captions: 'Cancelled checks & vouchers',
                  captionColor: 'text-blue-14',
              },
              {
                  key: 'totalSales',
                  title: 'Late Entry',
                  value: 0.00,
                  subVal: '',
                  type: '',
                  info: '',
                  icon: 'attach_money',
                  valueType: 'Number',
                  chartType: '',
                  color: 'bg-red-2',
                  iconBg: 'red-5',
                  captions: 'Late Entry Checks',
                  captionColor: 'text-blue-14',
              },
            ],
            eventList: [],

            // Calendar Sereies
            dateSereies: {},
            dateClicked: dateNow,
            modalComponents: {
                modalStatus: false,
                appId: 0,
                userDetails: {},
                modalTitle: 'Add Series of Reference to User'
            }
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    mounted(){
        // this.getSchedules().then((res) => {
        //     this.checkLoanStatuses()
        //     this.$nextTick(() => {
        //         this.getDashboard()
        //         this.getDailyDashboard()
        //     })
        // })
    },
    methods: {
        moment,
        handleDateClick(val){
            this.dateClicked = val.dateStr
            this.getSchedules()
        },
        addNewSeries(){
            this.modalComponents.modalStatus = true;
            this.modalComponents.userDetails.dateSelected = this.dateClicked;
        },
        updateModalStatus(){
            this.modalComponents.modalStatus = false;
        },
        async getSchedules(){
            this.calendarOptions.events = [];
            this.$q.loading.show();

            let payload = {
                userId: this.user.userId,
                date: this.dateClicked
            }

            api.post('misc/get/user/reference', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.dateSereies = data
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: "No Reference Found!",
                        icon: 'report_problem'
                    })
                }
            })

        },
        getDashboard(){
            const vm = this
            let payload = {
                userId: this.user.userId,
                userType: this.user.userType,
                currDate: dateNow
            }

            api.post('misc/dashboard', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.dashCards.map((el) => {
                        Object.keys(data).forEach((key) => {
                            if(el.key === key){
                                el.value = el.valueType === 'Number' ? vm.convertCurrency(data[key]) : Number(data[key])
                            }
                        })

                        return el
                    });
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

            this.$q.loading.hide();
        },
        getDailyDashboard(){
            const vm = this
            let payload = {
                userId: this.user.userId,
                userType: this.user.userType,
                currDate: dateNow
            }

            api.post('misc/dashboard/daily', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.dailyCards.map((el) => {
                        Object.keys(data).forEach((key) => {
                            if(el.key === key){
                                el.value = el.valueType === 'Number' ? vm.convertCurrency(data[key]) : Number(data[key])
                            }
                        })

                        return el
                    });
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

            this.$q.loading.hide();
        },
        checkLoanStatuses(){
            const vm = this
            let payload = {
                currDate: dateNow
            }

            api.post('misc/check/loans/expiry', payload).then((response) => {
                const data = {...response.data};
                if(data.error){
                   this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: 'Application update Failed',
                        icon: 'report_problem'
                    })
                }
            })
        },
        convertCurrency(value){
            let amount = Number(value);
            return amount.toLocaleString('en-US', {
                style: 'currency',
                currency: 'PHP',
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
