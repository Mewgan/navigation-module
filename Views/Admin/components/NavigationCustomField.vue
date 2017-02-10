<style>
    .navigation-custom-field .radio-inline span{
        margin-right: 20px;
    }
</style>

<template>
    <div class="navigation-custom-field">
        <div class="form-group row">
            <div class="col-md-3">
                <h4>Type de publication</h4>
                <p>Choisir le type de publication à afficher ou laisser vide pour tout afficher</p>
            </div>
            <div class="col-md-9">
               <select2 v-if="Object.keys(publication_types).length > 0"
                         :val="field.data.types"
                         @updateValue="updateValue" :emptyDefault="false"
                         :contents="publication_types"
                         :id="'navigation-type-select-' + line" index="id"
                         label="Type"></select2>
                <span v-else>Aucuns contenus</span>
            </div><!--end .col -->
        </div>
    </div>
</template>

<script type="text/babel">
    import {mapActions} from 'vuex'
    import {navigation_api} from '../api'

    export default{
        name: 'navigation-custom-field',
        components: {
            Select2: resolve => require(['../../../../../Blocks/AdminBlock/Front/components/Helper/Select2.vue'], resolve),
        },
        props: {
            field: {
                type: Object,
                required: true
            },
            line: {
                default: 'default'
            }
        },
        data(){
            return {
                publication_types: {}
            }
        },
        methods: {
            ...mapActions([
                'read'
            ]),
            updateValue(value){
                this.$set(this.field.data, 'types', value);
            }
        },
        created(){
            this.read({api: navigation_api.get_types_name }).then((response) => {
                this.publication_types = response.data;
            })
        },
        mounted(){
            if (!('tyoes' in this.field.data)) {
                this.field.data = {
                    types: []
                };
            }
        }
    }
</script>