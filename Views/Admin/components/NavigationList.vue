<style>

</style>

<template>
    <section class="list-navigation">
        <div class="section-header">
            <ol class="breadcrumb">
                <li class="active">Navigation</li>
            </ol>
            <button class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                <i class="fa fa-plus" aria-hidden="true"></i> Ajouter un menu
            </button>
        </div>
        <div class="section-body">
            <div class="card">

                <!-- BEGIN SEARCH HEADER -->
                <div class="card-head style-primary">
                    <div class="tools pull-left">
                        <form class="navbar-search search-item">
                            <div class="form-group">
                                <input type="text" class="form-control" v-model="search_value" placeholder="Recherche ...">
                            </div>
                            <a @click="search" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></a>
                        </form>
                        <a @click="refresh(resource.name)" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                    </div>
                    <div class="tools">
                        <router-link class="btn btn-floating-action btn-default" :to="{name: 'user-create'}"><i class="fa fa-plus"></i></router-link>
                    </div>
                </div><!--end .card-head -->
                <!-- END SEARCH HEADER -->

                <!-- BEGIN SEARCH RESULTS -->
                <div class="card-body">

                    <div class="row">

                        <div class="col-sm-12 col-md-12 col-lg-12">

                            <!-- BEGIN SEARCH RESULTS LIST -->
                            <div class="margin-bottom-xxl">
                                <label class="text-light text-lg">Lister : </label>
                                <select v-model.number="resource.max">
                                    <option v-for="option in max_options" :value="option">{{option}}</option>
                                </select>
                                <span class="text-light text-lg"> / Résultats : <strong>{{ resource.total }}</strong></span>
                                <div class="filter btn-group btn-group-sm pull-right">
                                    <button type="button" class="btn btn-default-light dropdown-toggle" data-toggle="dropdown">
                                        <span class="glyphicon glyphicon-arrow-down"></span> Filtre
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right animation-dock" role="menu">
                                        <li><a @click="setParams({resource: resource.name, key: 'order', value: {column:'a.first_name',dir:'asc'}})">Nom</a></li>
                                        <li><a @click="setParams({resource: resource.name, key: 'order', value: {column:'a.registered_at',dir:'asc'}})">Date d'inscription</a></li>
                                        <li><a @click="setParams({resource: resource.name, key: 'filter', value: {column:'a.state',operator:'eq',value:1}})">Actif</a></li>
                                        <li><a @click="setParams({resource: resource.name, key:'filter', value: {column:'a.state',operator:'eq',value:0}})">Inactif</a></li>
                                    </ul>
                                </div>
                                <div class="btn-group btn-group-sm pull-right">
                                    <button type="button" class="btn btn-default-light dropdown-toggle" data-toggle="dropdown">
                                        <span class="glyphicon glyphicon-arrow-down"></span> Sélection
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right animation-dock" role="menu">
                                        <li><a data-toggle="modal" data-target="#deleteUserModal">Supprimer</a></li>
                                        <li><a @click="updateUserState(1)">Activer</a></li>
                                        <li><a @click="updateUserState(0)">Désactiver</a></li>
                                    </ul>
                                </div>
                            </div><!--end .margin-bottom-xxl -->
                            <div class="list-results">
                                <div v-for="user in resource.data" class="col-xs-12 col-lg-6 hbox-xs">
                                    <div class="hbox-column width-2">
                                        <div class="checkbox checkbox-styled">
                                            <label>
                                                <input type="checkbox" :value="user.id" v-model="selected_items">
                                                <span></span>
                                            </label>
                                        </div>
                                        <img v-if="user.photo" height="80px" width="80px"  v-img="user.photo.path" :title="user.photo.title" class="user-photo img-circle img-responsive pull-left" :alt="user.photo.alt" />
                                    </div><!--end .hbox-column -->
                                    <div class="hbox-column v-top">
                                        <div class="clearfix">
                                            <div class="col-lg-12 margin-bottom-lg">
                                                <router-link :to="{name: 'user-read', params: {id: user.id}}" class="text-lg text-medium">{{ user.first_name }} {{ user.last_name }}</router-link>
                                            </div>
                                        </div>
                                        <div class="clearfix opacity-75">
                                            <div class="col-md-5">
                                                <span class="glyphicon glyphicon-phone text-sm"></span> &nbsp;{{ user.phone }}
                                            </div>
                                            <div class="col-md-7">
                                                <span class="glyphicon glyphicon-envelope text-sm"></span> &nbsp;{{ user.email }}
                                            </div>
                                        </div>
                                        <div class="clearfix opacity-75">
                                            <div class="col-md-5">
                                                <strong>Date de création :</strong> &nbsp;{{ user.registered_at.date | moment('DD/MM/YYYY') }}
                                            </div>
                                            <div class="col-md-7">
                                                <strong>Date d'expiration :</strong> &nbsp;{{ user.expiration_date.date | moment('DD/MM/YYYY') }}
                                            </div>
                                        </div>
                                        <div class="stick-top-right small-padding">
                                            <i v-show="user.state == 1"  class="fa fa-dot-circle-o fa-lg fa-fw text-success" data-toggle="tooltip" data-placement="left" data-original-title="Actif"></i>
                                            <i v-show="user.state == 0"  class="fa fa-dot-circle-o fa-lg fa-fw text-danger" data-toggle="tooltip" data-placement="left" data-original-title="Inactif"></i>
                                            <router-link :to="{name: 'user-read', params: {id: user.id}}" class="btn btn-floating-action btn-info tn-default-light"><i class="fa fa-pencil fa-fw" aria-hidden="true"></i></router-link>
                                            <button class="btn btn-floating-action btn-danger tn-default-light" data-toggle="modal" data-target="#deleteUserModal" @click="selectUser(user.id)"><i class="fa fa-trash fa-fw" aria-hidden="true"></i></button>
                                        </div>
                                    </div><!--end .hbox-column -->
                                </div><!--end .hbox-xs -->
                            </div><!--end .list-results -->
                            <!-- BEGIN SEARCH RESULTS LIST -->

                            <pagination :resource="resource"></pagination>
                        </div><!--end .col -->
                    </div><!--end .row -->
                </div><!--end .card-body -->
                <!-- END SEARCH RESULTS -->

            </div><!--end .card -->
            <!-- Modal Structure -->
            <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="deleteUserModalLabel">Suppression</h4>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer définitivement le compte sélectionné et le(s) site(s) associé(s) à ce compte ?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" @click="deleteUser()">Oui</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
        </div><!--end .section-body -->
    </section>
</template>


<script type="text/babel">

    import Pagination from '../../../../../Blocks/AdminBlock/Front/components/Helper/Pagination.vue'

    import {mapActions} from 'vuex'
    import {post_api, post_category_api} from '../api'

    export default
    {
        components: {Pagination},
        data () {
            return {
                website_id: this.$route.params.website_id,
                resource: {
                    url: post_api.all + this.$route.params.website_id,
                    name: 'posts_' + this.$route.params.website_id,
                    data: [],
                    max: 10,
                    total: 0
                },
                category: {},
                new_category: '',
                categories: {},
                search_value: '',
                selected_items: [],
                refresh_items: false,
                icon_class: '',
                max_options: [10, 20, 30]
            }
        },
        methods: {
            ...mapActions([
                'create', 'read', 'update','destroy', 'setParams', 'refresh', 'updateResourceValue', 'deleteResources'
            ]),
            search () {
                if (this.search_value !== ''){
                    this.setParams({resource: this.resource.name, key: 'search', value: this.search_value});
                    this.addClass('all');
                }
            },
            addClass (slug){
                $('.post-category').removeClass('active');
                $('.post-' + slug).addClass('active');
            },
            getIconClass (website) {
                return (this.website_id == website) ? 'fa fa-laptop' : 'fa fa-sitemap';
            },
            getIconTitle (content,website) {
                return (this.website_id == website) ? content + ' vient du site' : content + ' vient du thème parent';
            },
            selectPost (post){
                this.selected_items = [post.id];
            },
            updatePostState (state) {
                if (this.selected_items.length > 0) {
                    this.update({
                        api: post_api.change_state + this.website_id,
                        value: {
                            state: parseInt(state),
                            ids: this.selected_items
                        }
                    }).then((response) => {
                        if (response.data.status == 'success')
                            this.selected_items.forEach((id) => {
                                this.updateResourceValue({
                                    resource: this.resource.name,
                                    id,
                                    key: 'published',
                                    value: state
                                });
                            });
                        this.selected_items = [];
                        this.refresh_items = true;
                    });
                    this.refresh_items = false;
                }
            },
            changeState (post) {
                let state = (post.published == 0) ? 1 : 0;
                this.update({
                    api: post_api.change_state + this.website_id,
                    value: {
                        state,
                        ids: [post.id]
                    }
                }).then((response) => {
                    if (response.data.status == 'success')
                        this.updateResourceValue({
                            resource: this.resource.name,
                            id: post.id,
                            key: 'published',
                            value: state
                        });
                    this.refresh_items = true;
                });
                this.refresh_items = false;
            },
            deletePost () {
                if (this.selected_items.length > 0) {
                    this.deleteResources({
                        api: post_api.destroy + this.website_id,
                        resource: this.resource.name,
                        ids: this.selected_items
                    }).then(() => {
                        this.selected_items = [];
                    });
                }
            },
            selectCategory (category) {
                this.category = category;
            },
            createCategory () {
                if(this.new_category != '') {
                    this.create({
                        api: post_category_api.create + this.website_id,
                        value: {name: this.new_category}
                    }).then(() => {
                        this.loadCategory();
                    });
                }
            },
            updateCategory(){
                if(this.category.name != '') {
                    this.update({
                        api: post_category_api.update + this.category.id + '/' + this.website_id,
                        value: {name: this.category.name}
                    });
                }
            },
            deleteCategory(){
                this.destroy({
                    api: post_category_api.destroy + this.website_id,
                    ids: [this.category.id]
                }).then(() => {
                    this.loadCategory();
                });
            },
            loadCategory(){
                this.read({api: post_category_api.list_by_name + this.website_id}).then((response) => {
                    this.categories = response.data;
                })
            }
        },
        created () {
            this.loadCategory();
        },
        mounted () {
            let o = this;
            $(".search-item").submit(function (e) {
                e.preventDefault();
                o.search();
            });
        }
    }
</script>
