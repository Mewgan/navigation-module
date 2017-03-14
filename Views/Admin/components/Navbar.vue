<style>
    .nav-list .setup-bar, .dd-list .setup-bar {
        width: 100%;
    }
    .nav-list .setup-bar .tools i, .dd-list .setup-bar .tools i {
        margin: 0;
    }
    .nav-list .nav-bar span {
        text-transform: lowercase;
    }
    .nav-list button, .dd-list button {
        color: black;
    }
    .nav-list li {
        margin: 5px 0;
    }
    .nav-list li .panel, .dd-list .panel {
        border: none;
        margin: auto;
        box-shadow: none;
    }
    .nav-list li .dd-handle, .dd-list .dd-handle {
        margin: 12px 5px !important;
    }
    .nav-list header, .dd-list header {
        box-shadow: none !important;
        padding: 5px 25px;
        width: 70%;
    }
    .nav-list a, .dd-list .tools a {
        cursor: pointer;
        display: inline-block;
    }
    .nav-list .delete-item {
        cursor: pointer !important;
    }
    .nav-list .modal-btn{
        display: initial;
        margin: 0;
        cursor: pointer;
    }
    .nav-list .modal-btn.btn-primary{
        color: white;
    }
    .nav-list .modal-body p{
        text-transform: initial;
    }
</style>

<template>
    <li class="dd-item list-group"
        :data-id="item.id">
        <div class="dd-handle btn btn-default-light"><i class="fa fa-arrows"></i></div>
        <div class="btn setup-bar btn-default-light">
            <div class="card panel">
                <div class="card-head card-head-sm collapsed">
                    <header><input type="text" v-model="item.title"
                                   class="form-control"></header>
                    <div class="tools">
                        <em v-show="auth.status.level < 4">{{item.type}} </em>
                        <a class="btn btn-default" data-toggle="collapse"
                           :data-parent="accordion_parent"
                           :data-target="'#item-accordion-' + item.id"
                           aria-expanded="false"><i
                                class="fa fa-pencil"></i></a>
                        <a data-toggle="modal" :data-target="'#deleteNavBarModal' + item.id" class="btn delete-item btn-danger"><i
                                class="fa fa-trash"></i></a>
                    </div>
                </div>
                <div :id="'item-accordion-' + item.id" class="collapse nav-bar"
                     aria-expanded="false">
                    <div class="card-body">
                        <select2 v-if="item.type != 'custom'" :launch="true"
                                 :val="[item.type_id]" :multiple="false"
                                 @updateValue="updateUrl"
                                 :contents="getNavigationTypes(item.type)"
                                 :id="'url-item-select-' + item.id" index="name"
                                 label="Lien"></select2>
                        <div v-else class="form-group">
                            <input type="text" v-model="item.url"
                                   class="form-control" :id="'url-item-' + item.id">
                            <label :for="'url-item-' + item.id">Adresse web</label>
                        </div>
                        <div v-if="item.type != 'custom' && item.id === parseInt(item.id,10) && auth.status.level < 4"
                             class="form-group">
                            <input type="text" readonly :value="item.url"
                                   class="form-control" :id="'url-' + item.id">
                            <label :for="'url-' + item.id">Adresse web</label>
                        </div>
                        <div v-if="item.type != 'custom' && item.type != 'page' && auth.status.level < 4"
                             class="form-group">
                            <select v-model="item.route.id" :id="'url-route-select-' + item.id" class="form-control">
                                <option v-for="route in routes" :value="route.id">{{route.url}}</option>
                            </select>
                            <label :for="'url-route-select-' + item.id">Route</label>
                        </div>
                    </div>
                </div>

                <div class="modal fade" :id="'deleteNavBarModal' + item.id" tabindex="-1" role="dialog"
                     aria-labelledby="simpleModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title" :id="'deleteNavBarModalLabel' + item.id">Suppression</h4>
                            </div>
                            <div class="modal-body">
                                <p>Êtes-vous sûr de vouloir supprimer cette rubrique ?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="modal-btn btn btn-default" data-dismiss="modal">Non</button>
                                <button type="button" class="modal-btn btn btn-primary" data-dismiss="modal" @click="deleteItem">
                                    Oui
                                </button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div>

            </div>
        </div>
        <ol class="dd-list nav-list">
            <navbar v-if="'children' in item && item.children.length > 0"
                    @deleteItem="deleteNavBar"
                    v-for="(child, index) in item.children" :key="child.id" :item="child"
                    :accordion_parent="accordion_parent" :publication_types="publication_types"
                    :routes="routes" :navigation_website="navigation_website"
                    :website_id="website_id"></navbar>
        </ol>
    </li>
</template>

<script type="text/babel">

    import {mapGetters, mapActions} from 'vuex'
    import {navigation_api} from '../api'

    export default
    {
        name: 'navbar',
        components: {
            Select2: resolve => {
                require(['@front/components/Helper/Select2.vue'], resolve)
            }
        },
        props: {
            item: {
                required: true
            },
            accordion_parent: {
                default: '#menu-accordion-list'
            },
            publication_types: {
                required: true
            },
            routes: {
                required: true,
                type: Array
            },
            navigation_website: {
                required: true
            },
            website_id: {
                required: true
            }
        },
        computed: {
            ...mapGetters(['auth'])
        },
        methods: {
            ...mapActions(['destroy']),
            getNavigationTypes(type){
                return (this.publication_types[type] !== undefined && this.publication_types[type]['values'] !== undefined) ? this.publication_types[type]['values'] : [];
            },
            updateUrl(val){
                this.$set(this.item, 'type_id', val);
                this.$set(this.item, 'url', val);
            },
            deleteNavBar(id){
                let index = this.item.children.findIndex((i) => i.id == id);
                this.item.children.splice(index, 1);
            },
            deleteItem(){
                if (this.item.id === parseInt(this.item.id, 10) && this.navigation_website == this.website_id) {
                    this.destroy({
                        api: navigation_api.destroy_item + this.$route.params.website_id,
                        ids: [this.item.id]
                    });
                }else if(this.item.id.substring(0,6) == 'create' && this.item.children.length > 0){
                    this.destroy({
                        api: navigation_api.destroy_item + this.$route.params.website_id,
                        ids: this.getIds(this.item.children)
                    });
                }
                this.$emit('deleteItem', this.item.id);
            },
            getIds(items){
                let new_items = [];
                items.forEach((item) => {
                    if (item.id === parseInt(item.id, 10) && this.navigation_website == this.website_id) {
                        new_items.push(item.id);
                    }
                    if(item.children.length > 0){
                        new_items = new_items.concat(this.getIds(item.children));
                    }
                })
                return new_items;
            }
        }
    }
</script>