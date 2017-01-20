
export var routes = [
    {
        path: 'navigation',
        name: 'module:navigation',
        component:  resolve => require(['./components/NavigationList.vue'],resolve)
    },
    {
        path: 'navigation/:navigation_id',
        name: 'module:navigation:action',
        component:  resolve => require(['./components/NavigationAction.vue'],resolve)
    }
];


export var content_routes = {
    navigation:  (resolve) => { require(['./components/NavigationModule.vue'],resolve)}
};


export default {
    routes,
    content_routes
}