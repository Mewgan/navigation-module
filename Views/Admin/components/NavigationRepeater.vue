<style>
    .nav-list .setup-bar, .dd-list .setup-bar {
        width: 90%;
    }
    .nav-list .nav-bar span{
        text-transform: lowercase;
    }
    .nav-list .delete-item , .dd-list .delete-item {
        padding: 5px 10px 5px;
        position: absolute;
        top: 7px;
        right: 0;
        padding-left: 13px !important;
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
    }

    .nav-list a, .dd-list .tools a {
        cursor: pointer;
        display: inline-block;
    }
    .nav-list .delete-item{
        cursor: pointer !important;
    }
</style>

<template>
    <ol class="dd-list nav-list">
        <li v-for="(item, index) in items" class="dd-item list-group"
            :data-id="item.id">
            <div class="dd-handle btn btn-default-light"></div>
            <div class="btn setup-bar btn-default-light">
                <div class="card panel">
                    <div class="card-head card-head-sm collapsed" data-toggle="collapse"
                         :data-parent="accordion_parent"
                         :data-target="'#item-accordion-' + item.id"
                         aria-expanded="false">
                        <header>{{item.title}}</header>
                        <div class="tools">
                            <em>{{item.type}}</em>
                            <a class="btn btn-icon-toggle"><i
                                    class="fa fa-angle-down"></i></a>
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
                            <div v-if="item.type != 'custom' && item.id === parseInt(item.id,10)" class="form-group">
                                <input type="text" readonly :value="item.url"
                                       class="form-control" :id="'url-' + item.id">
                                <label :for="'url-' + item.id">Adresse web</label>
                            </div>
                            <div v-if="item.type != 'custom' && item.type != 'page'" class="form-group">
                                <select v-model="item.route.id" :id="'url-route-select-' + index" class="form-control">
                                    <option v-for="route in routes" :value="route.id">{{route.url}}</option>
                                </select>
                                <label :for="'url-route-select-' + index">Route</label>
                            </div>
                            <div class="form-group">
                                <input type="text" v-model="item.title"
                                       class="form-control" id="title-item">
                                <label for="title-item">Titre de la navigation</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a @click="deleteItem(index, item.id)" class="btn delete-item btn-danger"><i
                    class="fa fa-trash"></i></a>
            <navigation-repeater v-if="'children' in item && item.children.length > 0" :items="item.children"
                                 :accordion_parent="accordion_parent" :publication_types="publication_types"
                                 :routes="routes" :nestableClass="nestableClass" :navigation_website="navigation_website"></navigation-repeater>
        </li>
    </ol>
</template>

<script type="text/babel">

    import '../../../../../Blocks/AdminBlock/Resources/public/libs/nestable/nestable.css'

    import '../../../../../Blocks/AdminBlock/Resources/public/libs/nestable/jquery.nestable'
    import Select2 from '../../../../../Blocks/AdminBlock/Front/components/Helper/Select2.vue'

    import {mapActions} from 'vuex'
    import {navigation_api} from '../api'

    export default
    {
        name: 'navigation-repeater',
        components: {Select2},
        props: {
            items: {
                required: true,
                type: Array
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
            nestableClass: {
                default: '.nestable-list'
            },
            navigation_website: {
                required: true
            }
        },
        data () {
            return {
                website_id: this.$route.params.website_id
            }
        },
        methods: {
            ...mapActions([
                'destroy'
            ]),
            getNavigationTypes(type){
                return (type in this.publication_types && 'values' in this.publication_types[type]) ? this.publication_types[type]['values'] : [];
            },
            updateUrl(val, oldVal){
                let index = this.items.findIndex((key) => key.type_id == oldVal);
                if (index >= 0) {
                    this.$set(this.items[index],'type_id', val);
                    this.$set(this.items[index],'url', val);
                }
            },
            deleteItem(index, id){
                if (id === parseInt(id,10) && this.navigation_website == this.website_id) {
                    this.destroy({
                        api: navigation_api.destroy_item + this.$route.params.website_id,
                        ids: [id]
                    });
                }
                this.items.splice(index, 1);
            }
        },
        mounted(){
            this.$nextTick(function () {
                $(this.nestableClass).nestable({
                    maxDepth: 3
                });
            });
        }
    }
</script>
