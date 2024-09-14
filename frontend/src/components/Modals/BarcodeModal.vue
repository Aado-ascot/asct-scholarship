<template>
    <div class="q-pa-sm">
        <q-dialog persistent v-model="openModal">
            <q-card style="width: 700px; max-width: 80vw;">
                <q-card-section class="scroll text-center">
                    <BarcodeScanner 
                        @setResult="onDecode" 
                    />
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn flat label="Cancel" color="negative" @click="closeModal" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
import BarcodeScanner from '../Templates/BarcodeScan.vue'

export default{
    name: 'ScanCodeModal',
    components: {
        BarcodeScanner,
    },
    props: {
        modalStatus: {
            type: Boolean
        },
        productList: {
            type: Array,
            default: []
        },
    },
    data(){
        return {
            openModal: false,
            priceScan: "--"
        }
    },
    watch:{
        modalStatus: function(newVal){
            this.openModal = newVal
        },
        
    },
    methods: {
        async closeModal(){
            this.$emit('updateModalStatus');
        },
        async onDecode(result){
            this.$emit("generatedValue", result)
            this.closeModal()
        },
        // ending method
    },

}
</script>
