<style>
    .navigation-action .section-header {
        margin-bottom: 20px;
    }

    .navigation-action .section-header ol {
        float: left;
    }

    .navigation-action .section-header button {
        margin-left: 10px;
    }

    .navigation-action .nav-header header {
        width: 100%;
    }
    .navigation-action .nestable-list .setup-bar , .dd-list .setup-bar {
        width: 90%;
    }

    .navigation-action .nestable-list .delete-item , .dd-list .delete-item {
        padding: 5px 10px 5px;
        position: absolute;
        top: 7px;
        right: 0;
        padding-left: 13px !important;
    }
    .navigation-action .nestable-list button, .dd-list button {
        color: black;
    }

    .navigation-action .nestable-list li {
        margin: 5px 0;
    }

    .navigation-action .nestable-list li .panel, .dd-list .panel {
        border: none;
        margin: auto;
        box-shadow: none;
    }

    .navigation-action .nestable-list li .dd-handle, .dd-list .dd-handle {
        margin: 12px 5px !important;
    }

    .navigation-action .nestable-list header, .dd-list header {
        box-shadow: none !important;
    }

    .navigation-action .nestable-list a, .dd-list .tools a {
        cursor: pointer;
        display: inline-block;
    }
</style>

<template>
    <section class="navigation-action">
        <div class="section-header">
            <ol class="breadcrumb">
                <router-link :to="{name: 'module:navigation', params: {website_id: $route.params.website_id}}">
                    Menus
                </router-link>
                <li class="active">{{ navigation.title }}</li>
            </ol>
            <button class="btn ink-reaction btn-raised btn-lg btn-primary pull-right">
                <i class="fa fa-floppy-o" aria-hidden="true"></i> Sauvegarder
            </button>
            <button class="btn ink-reaction btn-raised btn-lg btn-danger pull-right">
                <i class="fa fa-trash" aria-hidden="true"></i> Supprimer
            </button>
        </div>
        <div class="section-body">
            <form class="form">

                <div class="col-sm-12 col-md-5 col-lg-4">
                    <div class="panel-group" id="menu-item-accordion">
                        <div class="card panel">
                            <div class="card-head card-head-sm collapsed" data-toggle="collapse"
                                 data-parent="#menu-item-accordion" data-target="#menu-accordion-custom"
                                 aria-expanded="false">
                                <header>Liens personnalisés</header>
                                <div class="tools">
                                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                </div>
                            </div>
                            <div id="menu-accordion-custom" class="collapse" aria-expanded="false">
                                <div class="card-body">
                                    <div class="form-group">
                                        <input type="text" v-model="nav_url" class="form-control" id="custom-link">
                                        <label for="custom-link">Lien</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control link-label" id="custom-link-label">
                                        <label for="custom-link-label">Texte du lien</label>
                                    </div>
                                    <a @click="addNavBar('menu-accordion-custom', 'custom')"
                                       class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                                        Ajouter
                                    </a>
                                </div>
                            </div>
                        </div><!--end .panel -->
                        <div class="card panel" v-for="(publication_type, index) in publication_types">
                            <div class="card-head card-head-sm collapsed" data-toggle="collapse"
                                 data-parent="#menu-item-accordion" :data-target="'#menu-accordion-' + index"
                                 aria-expanded="false">
                                <header>{{publication_type.plural}}</header>
                                <div class="tools">
                                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                </div>
                            </div>
                            <div :id="'menu-accordion-' + index" class="collapse" aria-expanded="false">
                                <div class="card-body">
                                    <select2 :launch="true" :multiple="false" @updateValue="updateItem"
                                             :contents="publication_type.values" :id="'item-select-' + index" key="name"
                                             label="Lien"></select2>
                                    <div class="form-group">
                                        <input type="text" class="form-control link-label"
                                               :id="'type-link-label-' + index">
                                        <label :for="'type-link-label-' + index">Texte du lien</label>
                                    </div>
                                    <a @click="addNavBar('menu-accordion-' + index, publication_type.id)"
                                       class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                                        Ajouter
                                    </a>
                                </div>
                            </div>
                        </div><!--end .panel -->
                    </div>
                </div>

                <div class="col-sm-12 col-md-7 col-lg-8">
                    <div class="card">
                        <div class="card-head nav-header">
                            <header>
                                <div class="form-group">
                                    <input type="text" class="form-control" v-model="navigation.name"
                                           placeholder="Nom du menu" id="title">
                                </div>
                            </header>
                        </div>
                        <!-- BEGIN SEARCH RESULTS -->
                        <div class="card-body style-primary">
                            <h2>Structure du menu</h2>

                            <p>Glissez chaque élément pour les placer dans l’ordre que vous préférez. Cliquez sur la
                                flèche à droite de l’élément pour afficher d’autres options de configuration.</p>

                            <div class="panel-group" id="menu-accordion-list">
                                <div class="dd nestable-list">
                                    <ol class="dd-list">
                                        <li v-for="(item, index) in navigation.items" class="dd-item list-group"
                                            :data-id="item.id">
                                            <div class="dd-handle btn btn-default-light"></div>
                                            <div class="btn setup-bar btn-default-light">
                                                <div class="card panel">
                                                    <div class="card-head card-head-sm collapsed" data-toggle="collapse"
                                                         data-parent="#menu-accordion-list"
                                                         :data-target="'#item-accordion-' + item.id" aria-expanded="false">
                                                        <header>{{item.title}}</header>
                                                        <div class="tools">
                                                            <a class="btn btn-icon-toggle"><i
                                                                    class="fa fa-angle-down"></i></a>
                                                        </div>
                                                    </div>
                                                    <div :id="'item-accordion-' + item.id" class="collapse"
                                                         aria-expanded="false">
                                                        <div class="card-body">
                                                            <select2 v-if="item.type != 'custom'" :launch="true" :val="[item.type_id]" :multiple="false" @updateValue="updateUrl"
                                                                     :contents="publication_types[item.type].values" :id="'url-item-select-' + item.id" key="name"
                                                                     label="Adresse web"></select2>
                                                            <div v-else class="form-group">
                                                                <input type="text" v-model="item.url" class="form-control" :id="'url-item-' + item.id">
                                                                <label :for="'url-item-' + item.id">Adresse web</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" v-model="item.title" class="form-control" id="title-item">
                                                                <label for="title-item">Titre de la navigation</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a @click="deleteNavBar(index, item.id)" class="btn delete-item btn-danger"><i
                                                    class="fa fa-trash"></i></a>
                                        </li>
                                    </ol>
                                </div>
                            </div><!--end .dd.nestable-list -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</template>

<script type="text/babel">

    import '../../../../../Blocks/AdminBlock/Resources/public/css/libs/nestable/nestable.css'

    import '../../../../../Blocks/AdminBlock/Resources/public/js/libs/nestable/jquery.nestable'
    import Select2 from '../../../../../Blocks/AdminBlock/Front/components/Helper/Select2.vue'

    import {mapActions} from 'vuex'
    import {website_api} from '../../../../../Blocks/AdminBlock/Front/api'
    import {navigation_api} from '../api'

    export default
    {
        components: {Select2},
        data () {
            return {
                website_id: this.$route.params.website_id,
                navigation_id: this.$route.params.navigation_id,
                navigation: {
                    name: '',
                    items: []
                },
                publication_types: [],
                nav_url: null
            }
        },
        methods: {
            ...mapActions([
                'read'
            ]),
            updateItem(val){
                this.nav_url = val;
            },
            updateUrl(val, oldVal){
                let index = this.navigation.items.findIndex((key) => key.type_id == oldVal);
                if(index >= 0) {
                    this.navigation.items[index]['type_id'] = val;
                    this.navigation.items[index]['url'] = val;
                }
            },
            addNavBar(bloc, type){
                let link = '';
                let type_id = null;
                let url = this.nav_url;
                if(type != 'custom')  type_id = this.nav_url;
                let item = {
                    id: 'create-' + this.navigation.items.length,
                    title: $('#' + bloc + ' .link-label').val(),
                    url: url,
                    type: type,
                    type_id: type_id,
                    position: this.navigation.items.length
                };
                this.navigation.items.push(item);
            },
            deleteNavBar(index, id){
                if (id.substring(0, 6) != 'create' && this.navigation.website.id == this.website_id) {

                }
                this.navigation.items.splice(index, 1);
            }
        },
        created () {
            if (this.navigation_id != 'create') {
                this.read({api: navigation_api.read + this.navigation_id + '/' + this.website_id}).then((response) => {
                    if ('resource' in response.data)
                        this.navigation = response.data.resource;
                })
            }
            this.read({api: website_api.get_publication_types + this.website_id}).then((response) => {
                if ('publication_types' in response.data)
                    this.publication_types = response.data.publication_types;
            });
        },
        mounted(){
            this.$nextTick(function () {
                $('.nestable-list').nestable();
            });
        }
    }
</script>
