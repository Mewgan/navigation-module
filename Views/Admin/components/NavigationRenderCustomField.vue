<template>
    <select :id="'link-select-' + id" class="form-control select2-list" v-model="content[content_key]" v-if="Object.keys(contents).length > 0">
        <optgroup v-if="isVisible(type)" v-for="(type, index) in contents" :label="type.name">
            <option v-for="value in type.values" :value="index + '@' + value.id">{{value.name}}</option>
        </optgroup>
    </select>
</template>

<script type="text/babel">

    import '../../../../../Blocks/AdminBlock/Resources/public/libs/select2/select2.css'
    import '../../../../../Blocks/AdminBlock/Resources/public/libs/select2/select2.min'

    import {navigation_api} from '../api'
    import {mapActions} from 'vuex'

    export default{
        name: 'navigation-render-custom-field',
        props: {
            field: {
                type: Object,
                required: true
            },
            id: {
                default: 'default'
            },
            content: {
                default: ''
            },
            content_key: {
                default: 'value'
            }
        },
        data(){
            return {
                website_id: this.$route.params.website_id,
                contents: []
            }
        },
        methods: {
            ...mapActions(['read']),
            isVisible(type){
                return (this.field.data.types.length == 0 || this.field.data.types.indexOf(type.id) >= 0) ? true : false;
            }
        },
        created(){
            this.read({api: navigation_api.get_types + this.website_id}).then((response) => {
                if('publication_types' in response.data){
                    this.contents = response.data.publication_types;
                }
            })
        },
        mounted(){
            if (!('types' in this.field.data)) {
                this.field.data = {
                    types: []
                };
            }
            let select2 = $("#link-select-" + this.id).select2();
            select2.on('change', (evt) => {
                this.$set(this.content, this.content_key, evt.val);
            });
        }
    }
</script>