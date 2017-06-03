<template>
    <div class="navigation-render-field">
        <select :id="'link-select-' + id" class="form-control select2-list" v-if="Object.keys(contents).length > 0">
            <option value="">Lien personnalisé</option>
            <optgroup v-if="isVisible(type)" v-for="(type, index) in contents" :label="type.name">
                <option v-for="value in type.values" :value="index + '@' + value.id">{{value.name}}</option>
            </optgroup>
        </select>
        <input type="text" v-model="content[content_key]" :id="'custom-link-' + id" class="form-control">
    </div>
</template>

<script type="text/babel">

    import '@admin/libs/select2/select2.css'
    import '@admin/libs/select2/select2.min'

    import {navigation_api} from '../../api'
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
        mounted(){
            let o = this;
            if (o.field.data.types === undefined) {
                o.field.data = {
                    types: []
                };
            }
            (o.content[o.content_key].substring(0,4) == 'http')
                ? $('#custom-link-' + o.id).show()
                : $('#custom-link-' + o.id).hide();
            o.read({api: navigation_api.get_types + o.website_id}).then((response) => {
                if(response.data.publication_types !== undefined){
                    o.contents = response.data.publication_types;
                }
            }).then(() => {
                let select2 = $("#link-select-" + o.id).select2();
                select2.val(o.content[o.content_key]).trigger('change');
                select2.on('change', (evt) => {
                    $('#custom-link-' + o.id).hide();
                    o.$set(o.content, o.content_key, evt.val);
                    if(evt.val == ""){
                        $('#custom-link-' + o.id).show();
                    }
                });
            })
        }
    }
</script>