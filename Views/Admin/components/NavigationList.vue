<style>
    .list-navigation .breadcrumb {
        display: inline-block;
    }
    .list-navigation .section-header .pull-right{
        margin-left: 10px;
    }
    .list-navigation .section-body {
        margin-top: 10px;;
    }
</style>

<template>
    <section class="list-navigation">

        <div class="alert alert-info" role="alert">
            <p><strong>Vous vous trouvez sur la page de gestion des menus</strong></p>
            <p>Il vous est possible de créer plusieurs menus pour les ajouter à différent endroit de votre site</p>
        </div>

        <div class="section-header">
            <ol class="breadcrumb">
                <li class="active">Menu</li>
            </ol>
            <router-link class="btn ink-reaction btn-raised btn-lg btn-info pull-right" :to="{name: 'module:navigation:action', params: {website_id: website_id, navigation_id: 'create'}}">
                <i class="fa fa-plus"></i> Ajouter un menu
            </router-link>
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-lg ink-reaction btn-primary">Action</button>
                <button type="button" class="btn btn-lg ink-reaction btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-caret-down"></i></button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a @click="deleteNavigation"><i class="fa fa-fw fa-times text-danger"></i> Supprimer</a></li>
                </ul>
            </div>
        </div>

        <div class="section-body">
            <div class="card">

                <!-- BEGIN SEARCH RESULTS -->
                <div class="card-body">

                    <div class="row">

                        <div class="col-sm-12 col-md-12 col-lg-12">

                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="checkbox checkbox-styled">
                                                    <label>
                                                        <input v-model="selectAll" type="checkbox">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </th>
                                            <th>Scope</th>
                                            <th>Nom</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="navigations.length == 0">
                                            <td colspan="4">Aucuns menus trouvés</td>
                                        </tr>
                                        <tr v-for="navigation in navigations">
                                            <td>
                                                <div class="checkbox checkbox-styled">
                                                    <label>
                                                        <input type="checkbox" :value="navigation.id"
                                                               v-model="selected_items">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="post-icon"><i
                                                        :title="getIconTitle('Ce menu',navigation.website)"
                                                        :class="getIconClass(navigation.website)"></i> </span>
                                            </td>
                                            <td>{{navigation.name}}</td>
                                            <td>
                                                <router-link
                                                        :to="{name: 'module:navigation:action', params: {website_id: website_id, navigation_id: navigation.id}}"
                                                        class="btn ink-reaction btn-floating-action btn-info">
                                                    <i class="fa fa-pencil"></i>
                                                </router-link>
                                                <a @click="selectNavigation(navigation.id)" data-toggle="modal"
                                                   data-target="#deleteNavigationModal"
                                                   class="btn ink-reaction btn-floating-action btn-danger"><i
                                                        class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div><!--end .col -->
                    </div><!--end .row -->
                </div><!--end .card-body -->
                <!-- END SEARCH RESULTS -->

            </div><!--end .card -->
            <!-- Modal Structure -->
            <div class="modal fade" id="deleteNavigationModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="deleteNavigationModalLabel">Suppression</h4>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer définitivement le(s) menu(s) sélectionné(s) ?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" @click="deleteNavigation()">Oui</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
        </div><!--end .section-body -->
    </section>
</template>


<script type="text/babel">

    import {mapActions} from 'vuex'
    import {navigation_api} from '../api'

    export default
    {
        data () {
            return {
                website_id: this.$route.params.website_id,
                navigations: [],
                selected_items: []
            }
        },
        computed: {
            selectAll: {
                get: function () {
                    return this.navigations ? this.selected_items.length == this.navigations.length : false;
                },
                set: function (value) {
                    this.selected_items = [];

                    if (value) {
                        this.navigations.forEach((item) => {
                            this.selected_items.push(item.id);
                        });
                    }
                }
            }
        },
        methods: {
            ...mapActions([
                'read', 'destroy'
            ]),
            getIconClass (website) {
                return (website != null && website.id !== undefined && this.website_id == website.id) ? 'fa fa-laptop' : 'fa fa-sitemap';
            },
            getIconTitle (content, website) {
                return (website != null && website.id !== undefined && this.website_id == website.id) ? content + ' vient du site' : content + ' vient du thème parent';
            },
            selectNavigation(id){
                this.selected_items = [id];
            },
            deleteNavigation(){
                this.destroy({
                    api: navigation_api.destroy + this.website_id,
                    ids: this.selected_items
                }).then(() => {
                    this.selected_items.forEach((el) => {
                        let index = this.navigations.findIndex((nav) => nav.id == el);
                        this.navigations.splice(index,1);
                    })
                    this.selected_items = [];
                });
            }
        },
        created () {
            this.read({api: navigation_api.all + this.website_id}).then((response) => {
                if(response.data.resource !== undefined)
                    this.navigations = response.data.resource;
            })
        }
    }
</script>
