<template>
    <div class="navigation-module">
        <form class="form">
            <h5 class="module-title">Information :</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" v-model="content.name" :id="'content-name-' + line">
                        <label :for="'content-name-' + line">Nom *</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" v-model="content.block" :id="'content-block-' + line">
                        <label :for="'content-block-' + line">Bloc *</label>
                    </div>
                </div>
            </div>
            <div v-show="auth.status.level < 4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.category.title" readonly
                                   :id="'content-module-' + line">
                            <label :for="'content-module-' + line">Module</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.name" readonly
                                   :id="'content-extension-' + line">
                            <label :for="'content-extension-' + line">Extension</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" v-model="content_data.class" :id="'content-class-' + line">
                    <label :for="'content-class-' + line">Class</label>
                </div>
                <h5 class="module-title">Choix du template :</h5>
                <template-editor @updateTemplate="updateTemplate" :id="line" :templates="templates" :template="content.template"
                                 label="Template du contenu"></template-editor>
            </div>
            <div>
                <h5 class="module-title">Choix du menu :</h5>
                <div class="form-group">
                    <select :id="'content-nav-' + line" v-model="content_data.navigation" class="form-control">
                        <option v-for="nav in navigations" :value="nav.id">{{nav.name}}</option>
                    </select>
                    <label :for="'content-nav-' + line">Le menu a afficher</label>
                </div>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button type="button" @click="updateContent" class="btn btn-primary">Enregistrer</button>
        </div>

    </div>
</template>

<script type="text/babel">

    import {mapGetters, mapActions} from 'vuex'

    import {template_api} from '@front/api'
    import {navigation_api} from '../../api'

    import module_mixin from '@front/mixin/module'

    export default
    {
        name: 'navigation',
        components: {
            TemplateEditor: resolve => { require(['@front/components/Helper/TemplateEditor.vue'], resolve) }
        },
        mixins: [module_mixin],
        props: {
            line: {
                default: 'default'
            },
            content: {
                type: Object,
                required: true
            },
            page: {
                default: null
            },
            website: {
                required: true
            }
        },
        data () {
            return {
                website_id: this.$route.params.website_id,
                content_data: {
                    class: '',
                    navigation: ''
                },
                navigations: [],
                templates: []
            }
        },
        watch: {
            'content_data': {
                handler(){
                    this.$set(this.content, 'data', this.content_data);
                },
                deep: true
            }
        },
        computed: {
            ...mapGetters(['auth'])
        },
        methods: {
            ...mapActions(['read', 'setResponse']),
            updateContent(){
                if (
                    this.content.template.id !== undefined &&
                    this.content.template.id != '' &&
                    this.content.data.navigation !== undefined &&
                    this.content.data.navigation != ''
                ) {
                    this.$emit('updateContent', this.content);
                    this.closeModal();
                } else
                    this.setResponse({status: 'error', message: 'Veuillez choisir le template et/ou le menu à afficher'});
            },
            updateTemplate(template){
                if (this.content.template !== undefined) this.content.template = template;
            }
        },
        created(){
            this.read({api: template_api.get_website_content_layouts + this.website}).then((response) => {
                this.templates = response.data;
            });
            this.read({api: navigation_api.all + this.website_id}).then((response) => {
                if (response.data.resource !== undefined) {
                    this.navigations = response.data.resource;
                    if (this.content.data.navigation !== undefined)this.content_data = this.content.data;
                }
            })
        }
    }
</script>