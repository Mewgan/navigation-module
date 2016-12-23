<style>
    .navigation-action .section-header{
        margin-bottom: 20px;
    }
    .navigation-action .section-header ol{
        float:left;
    }
    .navigation-action .section-header button{
        margin-left: 10px;
    }
    .navigation-action .nav-header header{
        width:100%;
    }
    .navigation-action .nestable-list button, .dd-list button{
        color: black;
    }
    .navigation-action .nestable-list li{
        margin: 5px 0;
    }
    .navigation-action .nestable-list li .panel, .dd-list .panel{
        border: none;
        margin: auto;
        box-shadow: none;
    }
    .navigation-action .nestable-list li .dd-handle , .dd-list .dd-handle{
        margin: 12px 5px !important;
    }
    .navigation-action .nestable-list header, .dd-list header{
        box-shadow: none !important;
    }
    .navigation-action .nestable-list a, .dd-list .tools a{
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
                                 data-parent="#menu-item-accordion" data-target="#menu-accordion-1" aria-expanded="false">
                                <header>Liens personnalisés</header>
                                <div class="tools">
                                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                </div>
                            </div>
                            <div id="menu-accordion-1" class="collapse" aria-expanded="false">
                                <div class="card-body">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="custom-link">
                                        <label for="custom-link">Lien</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="custom-link-label">
                                        <label for="custom-link-label">Texte du lien</label>
                                    </div>
                                    <button class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        </div><!--end .panel -->
                        <div class="card panel">
                            <div class="card-head card-head-sm collapsed" data-toggle="collapse"
                                 data-parent="#menu-item-accordion" data-target="#menu-accordion-2" aria-expanded="false">
                                <header>Pages</header>
                                <div class="tools">
                                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                </div>
                            </div>
                            <div id="menu-accordion-2" class="collapse" aria-expanded="false">
                                <div class="card-body">
                                    <select2 :launch="true" :multiple="false" @updateValue="updatePage" :contents="pages" id="pages-select" key="name" label="Lien"></select2>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="page-link-label">
                                        <label for="page-link-label">Texte du lien</label>
                                    </div>
                                    <button class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        </div><!--end .panel -->
                        <div class="card panel">
                            <div class="card-head card-head-sm collapsed" data-toggle="collapse"
                                 data-parent="#menu-item-accordion" data-target="#menu-accordion-3" aria-expanded="false">
                                <header>Articles</header>
                                <div class="tools">
                                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                </div>
                            </div>
                            <div id="menu-accordion-3" class="collapse" aria-expanded="false">
                                <div class="card-body">
                                    <select2 :launch="true" :multiple="false" @updateValue="updatePost" :contents="posts" id="posts-select" key="name" label="Lien"></select2>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="post-link-label">
                                        <label for="post-link-label">Texte du lien</label>
                                    </div>
                                    <button class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                                        Ajouter
                                    </button>
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
                                    <input type="text" class="form-control" v-model="navigation.name" placeholder="Nom du menu" id="title">
                                </div>
                            </header>
                        </div>
                        <!-- BEGIN SEARCH RESULTS -->
                        <div class="card-body style-primary">
                            <h2>Structure du menu</h2>

                            <p>Glissez chaque élément pour les placer dans l’ordre que vous préférez. Cliquez sur la
                                flèche à droite de l’élément pour afficher d’autres options de configuration.</p>

                            <div class="panel-group" id="menu-accordion">
                                <div class="dd nestable-list">
                                    <ol class="dd-list">
                                        <li v-for="nav in navigation.items" class="dd-item list-group" :data-id="nav.id">
                                            <div class="dd-handle btn btn-default-light"></div>
                                            <div class="btn btn-default-light">
                                                <div class="card panel">
                                                    <div class="card-head card-head-sm collapsed" data-toggle="collapse"
                                                         data-parent="#menu-accordion" :data-target="'#accordion-' + nav.id" aria-expanded="false">
                                                        <header>Liens internes</header>
                                                        <div class="tools">
                                                            <a class="btn btn-icon-toggle delete-item btn-danger"><i class="fa fa-trash"></i></a>
                                                            <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                                        </div>
                                                    </div>
                                                    <div :id="'accordion-' + nav.id" class="collapse" aria-expanded="false">
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="url-item">
                                                                <label for="url-item">Adresse web</label>
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" id="title-item">
                                                                <label for="title-item">Titre de la navigation</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
    import {page_api} from '../../../../../Blocks/AdminBlock/Front/api'
    import {post_api} from '../../../../Post/Views/Admin/api'
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
                pages: [],
                selectedPage: null,
                posts: [],
                selectedPost: null,
            }
        },
        methods: {
            ...mapActions([
                'read'
            ]),
            updatePage(val){
                this.selectedPage = val;
            },
            updatePost(val){
                this.selectedPost = val;
            },
            addNavBar(){

            }
        },
        created () {
            if (this.navigation_id != 'create') {
                this.read({api: navigation_api.read + this.navigation_id + '/' + this.website_id}).then((response) => {
                    if ('resource' in response.data)
                        this.navigation = response.data.resource;
                })
            }
            this.read({api: page_api.list_names + this.website_id}).then((response) => {
                if ('resource' in response.data)
                    this.pages = response.data.resource;
            })
            this.read({api: post_api.list_names + this.website_id}).then((response) => {
                if ('resource' in response.data)
                    this.posts = response.data.resource;
            })
        },
        mounted(){
            this.$nextTick(function () {
                $('.nestable-list').nestable();
            });
        }
    }
</script>
