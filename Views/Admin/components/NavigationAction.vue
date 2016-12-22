<style>
    .navigation-action .nav-header header{
        width:100%;
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
        </div>
        <div class="section-body">
            <form class="form-horizontal">
                <div class="col-sm-12 col-md-5 col-lg-4">
                    <div class="panel-group" id="accordion3">
                        <div class="card panel">
                            <div class="card-head card-head-sm collapsed" data-toggle="collapse" data-parent="#accordion3" data-target="#accordion3-1" aria-expanded="false">
                                <header>Liens personnalisés</header>
                                <div class="tools">
                                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                </div>
                            </div>
                            <div id="accordion3-1" class="collapse" aria-expanded="false">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="link" class="col-sm-1 control-label">Lien</label>
                                        <div class="col-sm-11">
                                            <input type="text" class="form-control" id="link">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="link-label" class="col-sm-2 control-label">Texte du lien</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="link-label">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!--end .panel -->
                        <div class="card panel">
                            <div class="card-head card-head-sm collapsed" data-toggle="collapse" data-parent="#accordion3" data-target="#accordion3-3" aria-expanded="false">
                                <header>Liens internes</header>
                                <div class="tools">
                                    <a class="btn btn-icon-toggle"><i class="fa fa-angle-down"></i></a>
                                </div>
                            </div>
                            <div id="accordion3-3" class="collapse" aria-expanded="false">
                                <div class="card-body">						<p>Per at postea mediocritatem, vim numquam aliquid eu, in nam sale gubergren. Fuisset delicatissimi duo, qui ut animal noluisse erroribus. Ea eum veniam audire. Dicant vituperata consequuntur.</p>
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
                                    <input type="text" class="form-control"  placeholder="Nom du menu" id="title">
                                </div>
                            </header>
                        </div>
                        <!-- BEGIN SEARCH RESULTS -->
                        <div class="card-body style-primary">
                            <h2>Structure du menu</h2>

                            <p>Glissez chaque élément pour les placer dans l’ordre que vous préférez. Cliquez sur la flèche à droite de l’élément pour afficher d’autres options de configuration.</p>
                            <div class="dd nestable-list">
                                    <ol class="dd-list">
                                        <li class="dd-item list-group" data-id="8">
                                            <div class="dd-handle btn btn-default-light"></div>
                                            <div class="btn btn-default-light">Item 8</div>
                                        </li>
                                        <li class="dd-item list-group" data-id="9">
                                            <div class="dd-handle btn btn-default-light"></div>
                                            <div class="btn btn-default-light">Item 9</div>
                                        </li>
                                        <li class="dd-item list-group" data-id="10">
                                            <div class="dd-handle btn btn-default-light"></div>
                                            <div class="btn btn-default-light">Item 10</div>
                                        </li>
                                        <li class="dd-item list-group" data-id="11">
                                            <div class="dd-handle btn btn-default-light"></div>
                                            <div class="btn btn-default-light">Item 11</div>

                                            <ol class="dd-list">
                                                <li class="dd-item list-group" data-id="12">
                                                    <div class="dd-handle btn btn-default-light"></div>
                                                    <div class="btn btn-default-light">Item 12</div>
                                                </li>
                                                <li class="dd-item list-group" data-id="13">
                                                    <div class="dd-handle btn btn-default-light"></div>
                                                    <div class="btn btn-default-light">Item 13</div>
                                                </li>
                                                <li class="dd-item list-group" data-id="14">
                                                    <div class="dd-handle btn btn-default-light"></div>
                                                    <div class="btn btn-default-light">Item 14</div>
                                                </li>
                                            </ol>
                                        </li>
                                    </ol>
                                </div><!--end .dd.nestable-list -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</template>

<script type="text/babel">

    import '../../../../../Blocks/AdminBlock/Resources/public/js/libs/jquery-ui/jquery-ui.min'
    import '../../../../../Blocks/AdminBlock/Resources/public/js/libs/nestable/jquery.nestable'

    import '../../../../../Blocks/AdminBlock/Resources/public/css/libs/jquery-ui/jquery-ui-theme.css'
    import '../../../../../Blocks/AdminBlock/Resources/public/css/libs/nestable/nestable.css'

    import {mapActions} from 'vuex'
    import {navigation_api} from '../api'

    export default
    {
        data () {
            return {
                website_id: this.$route.params.website_id,
                navigation: {}
            }
        },
        methods: {
            ...mapActions([
                'read'
            ]),

        },
        created () {

        },
        mounted(){
            $('.nestable-list').nestable();
        }
    }
</script>
