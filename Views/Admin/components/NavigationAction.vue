<style>
    .navigation-action .section-header {
        margin-bottom: 20px;
    }

    .navigation-action .section-header ol {
        float: left;
    }

    .navigation-action > .section-header > a {
        margin-left: 10px;
    }

    .navigation-action .nav-header header {
        width: 100%;
    }

    .navigation-action .cursor {
        cursor: pointer;
    }

</style>

<template>
    <section class="navigation-action style-default-light auto">

        <div class="section-header">
            <ol class="breadcrumb">
                <li>
                    <router-link :to="{name: 'module:navigation', params: {website_id: $route.params.website_id}}">
                        Menus
                    </router-link>
                </li>
                <li class="active">{{ navigation.name }}  <a data-toggle="modal" data-target="#infoNavigationModal"><i class="fa fa-info-circle"></i></a></li>
            </ol>
            <a v-if="auth.status.level < 4"  data-toggle="modal"
               data-target="#deleteNavigationModal" class="btn btn-default-bright pull-right">
                <i class="fa fa-trash" aria-hidden="true"></i> Supprimer
            </a>
        </div>

        <div class="section-body">

            <form class="form">

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
                            <p>Glissez chaque élément pour les placer dans l’ordre que vous préférez.</p>
                            <div class="panel-group" id="menu-accordion-list">
                                <div class="dd nestable-list">
                                    <ol class="dd-list nav-list">
                                        <navbar v-for="(item, index) in navigation.items" :key="item.id" :item="item"
                                                @deleteItem="deleteNavBar"
                                                :navigation_website="navigation.website.id"
                                                :publication_types="publication_types"
                                                :routes="routes"
                                                :website_id="website_id"></navbar>
                                    </ol>
                                </div>
                            </div><!--end .dd.nestable-list -->

                            <a @click="updateOrCreate" class="btn btn-default-bright pull-right">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i> Enregistrer
                            </a>

                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-5 col-lg-4">
                    <h2 class="text-primary">Ajouter une rubrique au menu</h2>

                    <div class="panel-group" id="menu-item-accordion">

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
                                    <select2 :multiple="false" @updateValue="updateItem"
                                             :contents="publication_type.values" :val_index="false"
                                             :id="'item-select-' + index"
                                             index="name"
                                             :label="publication_type.name"></select2>
                                    <select2 v-if="publication_type.id != 'page' && routes.length > 0 && auth.status.level < 4"
                                            :multiple="false"
                                            @updateValue="updateRoute"
                                            :val="('route_id' in publication_type) ? [publication_type.route_id] : []"
                                            :contents="routes" :id="'route-select-' + index" index="url"
                                            label="Route"></select2>
                                    <a @click="addNavBar(publication_type.id)"
                                       class="btn btn-default pull-right">
                                        Ajouter au menu
                                    </a>
                                </div>
                            </div>
                        </div>

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
                                        <input type="text" v-model="nav_title" class="form-control link-label"
                                               id="custom-link-label">
                                        <label for="custom-link-label">Texte du lien</label>
                                    </div>
                                    <a @click="addNavBar('custom')"
                                       class="btn btn-default pull-right">
                                        Ajouter au menu
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>

        <div class="modal fade" id="deleteNavigationModal" tabindex="-1" role="dialog"
             aria-labelledby="simpleModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="deleteNavigationModalLabel">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer définitivement le menu sélectionné(s) ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="deleteNavigation()">
                            Oui
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        <!-- Modal Structure -->
        <div class="modal fade" id="infoNavigationModal" tabindex="-1" role="dialog"
             aria-labelledby="simpleModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="infoNavigationModalLabel">Information</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" role="alert">
                            <strong><i class="fa fa-info-circle"></i> Comment ajouter une rubrique à mon menu ?</strong>
                            <p><strong>1.</strong> Choisir le type de rubrique que vous souhaitez ajouter au menu (page, article,
                                catégorie ...). Pensez à créer d'abord l'élément au préalable</p>
                            <p><strong>2.</strong> Ensuite choisir votre rubrique</p>
                            <p><strong>3.</strong> Et enfin ajouter votre rubrique au menu en cliquant sur le bouton "Ajouter"</p>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

    </section>

</template>

<script type="text/babel">

    import '@admin/libs/nestable/nestable.css'
    import '@admin/libs/nestable/jquery.nestable'

    import {mapGetters, mapActions} from 'vuex'
    import {route_api} from '@front/api'
    import {navigation_api} from '../api'

    export default
    {
        components: {
            Select2: resolve => {
                require(['@front/components/Helper/Select2.vue'], resolve)
            },
            Navbar: resolve => {
                require(['./Navbar.vue'], resolve)
            }
        },
        data () {
            return {
                website_id: this.$route.params.website_id,
                navigation_id: this.$route.params.navigation_id,
                navigation: {
                    name: '',
                    website: {id: ''},
                    items: []
                },
                publication_types: [],
                nav_url: null,
                nav_title: null,
                routes: [],
                nav_route: null
            }
        },
        computed: {
            ...mapGetters(['auth'])
        },
        methods: {
            ...mapActions([
                'read', 'update', 'destroy'
            ]),
            updateItem(val){
                if (val.id !== undefined) this.nav_url = val.id;
                if (val.name !== undefined) this.nav_title = val.name;
            },
            updateRoute(val){
                this.nav_route = val;
            },
            addNavBar(type){
                let type_id = null;
                if (this.nav_route == null) {
                    this.nav_route = (this.publication_types[type] !== undefined && this.publication_types[type]['route_id'] !== undefined)
                            ? this.publication_types[type]['route_id']
                            : null;
                }
                let url = this.nav_url;
                if (this.nav_title != null && (type == 'custom' || type == 'page' || this.nav_route != null) && this.nav_url != null) {
                    (type != 'custom') ? type_id = this.nav_url : this.nav_route = null;
                    let item = {
                        id: 'create-' + this.navigation.items.length,
                        title: this.nav_title,
                        url: url,
                        route: {id: this.nav_route},
                        type: type,
                        parent: null,
                        children: [],
                        type_id: type_id,
                        position: this.navigation.items.length
                    };
                    this.navigation.items.push(item);
                }
            },
            deleteNavBar(id){
                let index = this.navigation.items.findIndex((i) => i.id == id);
                this.navigation.items.splice(index, 1);
            },
            updateOrCreate(){
                this.update({
                    api: navigation_api.update_or_create + this.website_id + '/' + this.navigation_id,
                    value: {
                        name: this.navigation.name,
                        items: this.navigation.items
                    }
                }).then((response) => {
                    if (response.data.resource !== undefined) {
                        this.navigation = response.data.resource;
                        if (this.navigation_id != response.data.resource.id) {
                            this.$router.replace({
                                name: 'module:navigation:action',
                                params: {website_id: this.website_id, navigation_id: response.data.resource.id}
                            });
                        }
                        this.navigation_id = (this.navigation.id !== undefined) ? this.navigation.id : 'create';
                    }
                })
            },
            deleteNavigation(){
                if (this.navigation.id !== undefined && this.auth.status.level < 4) {
                    this.destroy({
                        api: navigation_api.destroy + this.website_id,
                        ids: [this.navigation.id]
                    }).then((response) => {
                        if (response.data.status == 'success') {
                            this.$router.replace({name: 'module:navigation', params: {website_id: this.website_id}})
                        }
                    });
                }
            },
            reorder(navs, items, new_items){
                navs.forEach((el, key) => {
                    let item = this.getItem(items, el.id);
                    new_items[key] = {
                        id: item.id,
                        title: item.title,
                        url: item.url,
                        route: item.route,
                        type: item.type,
                        parent: item.parent,
                        children: [],
                        type_id: item.type_id,
                        position: key
                    };
                    if (el.children !== undefined) {
                        new_items[key]['children'] = this.reorder(el.children, items, new_items[key]['children'])
                    }
                });
                return new_items;
            },
            getItem(items, id){
                for (let i = 0; i < items.length; i++) {
                    if (items[i]['id'] !== undefined && items[i]['id'] == id) {
                        return items[i];
                    }
                    else if (items[i]['children'] !== undefined && items[i]['children'].length > 0) {
                        let found = this.getItem(items[i]['children'], id);
                        if (found) return found;
                    }
                }
            }
        },
        created () {
            this.read({api: navigation_api.get_types + this.website_id}).then((response) => {
                if (response.data.publication_types !== undefined)
                    this.publication_types = response.data.publication_types;
            }).then(() => {
                this.read({api: route_api.get_website_routes + this.website_id}).then((response) => {
                    if (response.data.resource !== undefined)
                        this.routes = response.data.resource;
                }).then(() => {
                    if (this.navigation_id != 'create') {
                        this.read({api: navigation_api.read + this.navigation_id + '/' + this.website_id}).then((response) => {
                            if (response.data.resource !== undefined) {
                                this.navigation = response.data.resource;
                            }
                        })
                    }
                });
            });
        },
        mounted(){

            let o = this;
            $('.nestable-list').nestable({
                maxDepth: 3,
                onDrop: () => {
                    let navs = $('.nestable-list').nestable('serialize');
                    o.navigation.items = o.reorder(navs, o.navigation.items, []);
                }
            });
        }
    }
</script>