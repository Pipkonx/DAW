require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('dashboard', require('./components/Dashboard.vue').default);
Vue.component('welcome', require('./components/Welcome.vue').default);
Vue.component('transactions-index', require('./components/TransactionsIndex.vue').default);
Vue.component('expenses-index', require('./components/ExpensesIndex.vue').default);
Vue.component('social-index', require('./components/SocialIndex.vue').default);
Vue.component('categories-index', require('./components/CategoriesIndex.vue').default);
Vue.component('profile-show', require('./components/ProfileShow.vue').default);
Vue.component('profile-edit', require('./components/ProfileEdit.vue').default);
Vue.component('plans-index', require('./components/PlansIndex.vue').default);
Vue.component('profile-security', require('./components/ProfileSecurity.vue').default);
Vue.component('two-factor-challenge', require('./components/TwoFactorChallenge.vue').default);
Vue.component('famous-portfolio-show', require('./components/FamousPortfolioShow.vue').default);
Vue.component('ai-analyst-index', require('./components/AiAnalystIndex.vue').default);
Vue.component('markets-index', require('./components/MarketsIndex.vue').default);
Vue.component('asset-show', require('./components/AssetShow.vue').default);
Vue.component('support-index', require('./components/SupportIndex.vue').default);
Vue.component('support-show', require('./components/SupportShow.vue').default);
Vue.component('financial-planning-index', require('./components/FinancialPlanningIndex.vue').default);
Vue.component('admin-dashboard', require('./components/AdminDashboard.vue').default);
Vue.component('admin-tickets-index', require('./components/AdminTicketsIndex.vue').default);
Vue.component('admin-reports', require('./components/AdminReports.vue').default);
Vue.component('admin-analytics', require('./components/AdminAnalytics.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
