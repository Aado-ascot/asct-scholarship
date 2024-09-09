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
                        <span class="text-h6 text-bold">Check Voucher Overview</span><br/>
                        <!-- <span class="text-caption text-grey">Invoice & Check summary</span><br/> -->

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
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                
                <q-card
                    flat
                    class="my-card bg-white"
                >
                    <q-card-section>
                        <div class="text-right q-mb-md">
                            <q-btn 
                                @click="openAddCheckModal = !openAddCheckModal" 
                                color="secondary" 
                                icon="ti-plus"
                                no-caps 
                                label="Add New Voucher" 
                            />
                        </div>
                        <div v-if="tableLoading && rows.length === 0" class="text-center">
                            <q-spinner-bars
                                color="primary"
                                size="3em"
                            />
                        </div>

                        <div 
                            v-if="!tableLoading && rows.length === 0" 
                            class="text-center q-pa-md"
                        >
                            <q-icon color="grey-4" name="ti-dropbox-alt" size="6em" /> <br/>
                            <span class="text-caption text-grey-8">
                                No Data Can Be Shown.
                            </span>
                        </div>
                        <q-table
                            v-if="rows.length > 0"
                            flat bordered
                            :rows="rows"
                            wrap-cells
                            :columns="columns"
                            row-key="cvNumber"
                            separator="cell"
                        >  
                            <template v-slot:header="props">
                                <q-tr :props="props">
                                    <q-th auto-width />
                                    <q-th
                                        v-for="col in props.cols"
                                        :key="col.name"
                                        :props="props"
                                    >
                                        {{ col.label }}
                                    </q-th>
                                </q-tr>
                            </template>
                            <template v-slot:body="props">
                                <q-tr :props="props">
                                    <q-td auto-width>
                                        <q-btn 
                                            size="sm" 
                                            color="accent" 
                                            round 
                                            dense 
                                            @click="props.expand = !props.expand" 
                                            :icon="props.expand ? 'ti-close' : 'ti-eye'" 
                                        />
                                    </q-td>
                                    <q-td
                                        v-for="col in props.cols"
                                        :key="col.name"
                                        :props="props"
                                    >
                                        {{ col.value }}
                                    </q-td>
                                </q-tr>
                                <q-tr v-show="props.expand" :props="props">
                                    <q-td colspan="100%">
                                        
                                        {{props.row }}
                                        <div class="row">
                                            <div class="col-12 q-pa-xs text-bold text-h6">
                                                <span class="text-title">Other Check Details</span>
                                                <q-separator />
                                            </div>

                                            <div class="col-6 q-pa-xs">
                                                <span class="text-title text-bold">{{ convertCurrency(props.row.expandedWTax) }}</span><br/>
                                                <span class="text-caption text-grey">Expanded Withholding Tax</span>
                                            </div>
                                            <div class="col-6 q-pa-xs">
                                                <span class="text-title text-bold">{{ convertCurrency(props.row.inputTax) }}</span><br/>
                                                <span class="text-caption text-grey"> Input Tax </span>
                                            </div>
                                            <div class="col-6 q-pa-xs">
                                                <span class="text-title text-bold">{{ convertCurrency(props.row.transAndTravel) }}</span><br/>
                                                <span class="text-caption text-grey"> Transportation & Travel</span>
                                            </div>
                                            <div class="col-6 q-pa-xs">
                                                <span class="text-title text-bold">{{ convertCurrency(props.row.utilities) }}</span><br/>
                                                <span class="text-caption text-grey"> Communication, Light & Water</span>
                                            </div>

                                            <div class="col-12 q-pa-xs text-bold text-h6">
                                                <span class="text-title">Account Titles</span>
                                                <q-separator />
                                            </div>
                                        </div>
                                    </q-td>
                                </q-tr>
                            </template>
                        </q-table>
                    </q-card-section>
                </q-card>
            </div>
        </div>

        <addCheckModal 
            :modalStatus="openAddCheckModal"
            @updateModalStatus="closeClientModal"
            @refreshData="getCheckVouchers"
        />
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';
import addCheckModal from '../../components/Modals/AddCheckVoucher.vue'

export default {
    name: 'UserCheckVoucherPage',
    components: {
        addCheckModal
    },
    data(){
        return {
            rows: [],
            tableLoading: false,
            openAddCheckModal: false,
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

            
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        },
        columns(){
            return [
                {
                    name: 'payee',
                    required: true,
                    label: 'Payee',
                    align: 'left',
                    field: row => row.payee,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'particular', align: 'left', label: 'Particulars', field: 'particulars' },
                { name: 'checkDate', align: 'center', label: 'Date', field: 'checkDate', sortable: true },
                { name: 'cvNumber', label: 'CV No.', field: 'cvNumber', sortable: true },
                {
                    name: 'bankId',
                    required: true,
                    label: 'Bank',
                    align: 'left',
                    field: row => row.bankId.label,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'checkNumber', label: 'Check No.', field: 'checkNumber', sortable: true },
                {
                    name: 'cashInBank',
                    required: true,
                    label: 'Bank',
                    align: 'left',
                    field: row => row.cashInBank,
                    format: val => `${this.convertCurrency(val)}`,
                    sortable: true
                }
            ]
        }
    },
    mounted(){
        this.getCheckVouchers()
    },
    methods: {
        moment,
        closeClientModal(){
            this.openAddCheckModal = false;
        },
        getCheckVouchers(){
            const vm = this
            this.$api.get('voucher/get/list').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.rows = data.list ? data.list : [];
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
