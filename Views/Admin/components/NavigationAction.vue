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
    .navigation-action .cursor{
        cursor: pointer;
    }
</style>

<template>
    <section class="navigation-action">
        <div class="section-header">
            <ol class="breadcrumb">
                <li>
                    <router-link :to="{name: 'module:navigation', params: {website_id: $route.params.website_id}}">
                        Menus
                    </router-link>
                </li>
                <li class="active">{{ navigation.name }}</li>
            </ol>
            <a @click="updateOrCreate" class="btn ink-reaction btn-raised btn-lg btn-primary pull-right">
                <i class="fa fa-floppy-o" aria-hidden="true"></i> Sauvegarder
            </a>
            <a class="btn ink-reaction btn-raised btn-lg btn-danger pull-right">
                <i class="fa fa-trash" aria-hidden="true"></i> Supprimer
            </a>
        </div>
        <div class="section-body">
            <div class="alert alert-info" role="alert">
                <strong>Comment ajouter une rubrique à mon menu ?</strong><br/>
                <p><strong>1.</strong> Choisir le type de lien que vous souhaitez ajouter au menu (page, article, catégorie ...). Pensez à créer d'abord l'élément au préalable</p>
                <p><strong>2.</strong> Ensuite choisir ou saisir votre lien</p>
                <p><strong>3.</strong> Et enfin ajouter votre lien au menu en cliquant sur le bouton "Ajouter"</p>
            </div>

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

                            <p>Glissez chaque élément pour les placer dans l’ordre que vous préférez. Cliquez sur la
                                flèche à droite de l’élément pour afficher d’autres options de configuration.</p>
                            <div class="panel-group" id="menu-accordion-list">
                                <div class="dd nestable-list">
                                    <navigation-repeater :items="navigation.items"
                                                         :navigation_website="navigation.website.id"
                                                         :publication_types="publication_types"
                                                         :routes="routes"></navigation-repeater>
                                </div>
                            </div><!--end .dd.nestable-list -->
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-5 col-lg-4">
                    <h2 class="text-primary">Liens</h2>

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
                                    <select2 :launch="true" :multiple="false" @updateValue="updateItem"
                                             :contents="publication_type.values" :val_index="false" :id="'item-select-' + index"
                                             index="name"
                                             :label="publication_type.name"></select2>
                                    <select2 v-if="publication_type.id != 'page' && routes.length > 0 && auth.status.level < 4" :launch="true" :multiple="false"
                                             @updateValue="updateRoute" :val="('route_id' in publication_type) ? [publication_type.route_id] : []"
                                             :contents="routes" :id="'route-select-' + index" index="url"
                                             label="Route"></select2>
                                    <a @click="addNavBar('menu-accordion-' + index, publication_type.id)"
                                       class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
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
                                        <input type="text" v-model="nav_title" class="form-control link-label" id="custom-link-label">
                                        <label for="custom-link-label">Texte du lien</label>
                                    </div>
                                    <a @click="addNavBar('menu-accordion-custom', 'custom')"
                                       class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                                        Ajouter au menu
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </section>
</template>

<script type="text/babel">
    import '../../../../../Blocks/AdminBlock/Resources/public/libs/nestable/nestable.css'
    import '../../../../../Blocks/AdminBlock/Resources/public/libs/nestable/jquery.nestable'
    import Select2 from '../../../../../Blocks/AdminBlock/Front/components/Helper/Select2.vue'
    import NavigationRepeater from './NavigationRepeater.vue'
    import {mapGetters, mapActions} from 'vuex'
    import {route_api} from '../../../../../Blocks/AdminBlock/Front/api'
    import {navigation_api} from '../api'
    export default
    {
        components: {Select2, NavigationRepeater},
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
            ...mapGetters(['auth']),
        },
        methods: {
            ...mapActions([
                'read', 'update', 'destroy'
            ]),
            updateItem(val){
                if(val.id !== undefined) this.nav_url = val.id;
                if(val.name !== undefined) this.nav_title = val.name;
            },
            updateRoute(val){
                this.nav_route = val;
            },
            addNavBar(bloc, type){
                let link = '';
                let type_id = null;
                if(this.nav_route == null) this.nav_route = (this.publication_types[type] !== undefined && this.publication_types[type]['route_id'] !== undefined) ? this.publication_types[type]['route_id'] : null;
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
            updateOrCreate(){
                let navs = $('.nestable-list').nestable('serialize');
                let new_items = this.reorder(navs, this.navigation.items, []);
                this.update({
                    api: navigation_api.update_or_create + this.website_id + '/' + this.navigation_id,
                    value: {
                        name: this.navigation.name,
                        items: new_items
                    }
                }).then((response) => {
                    if (response.data.status == 'success'){
                        if(this.navigation_id == 'create')
                            this.$router.push({name: 'module:navigation:action', params: {website_id: this.website_id, navigation_id: response.data.resource.id}});
                        else
                            this.navigation = response.data.resource;
                    }
                })
            },
            deleteNavigation(){
                if(this.navigation.id !== undefined){
                    this.destroy({
                        api: navigation_api.destroy + this.website_id,
                        ids: [this.navigation.id]
                    }).then((response) => {
                        if (response.data.status == 'success') {
                            this.$router.push({name: 'module:navigation', params: {website_id: this.website_id}})
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
                for(let i = 0; i < items.length; i++){
                    if(items[i]['id'] !== undefined && items[i]['id'] == id) {
                        return items[i];
                    }
                    else if(items[i]['children'] !== undefined && items[i]['children'].length > 0) {
                        let found = this.getItem(items[i]['children'], id);
                        if(found) return found;
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
                            if (response.data.resource !== undefined)
                                this.navigation = response.data.resource;
                        })
                    }
                });
            });
        },
        mounted(){
            this.$nextTick(function () {
                $('.nestable-list').nestable({
                    maxDepth: 3
                });
            });
        }
    }
</script>