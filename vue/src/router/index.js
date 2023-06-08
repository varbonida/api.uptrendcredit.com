import {createRouter, createWebHistory} from "vue-router";

import AddAccount from '../views/AddAccount.vue';
import Dashboard from '../views/Dashboard.vue';
import Transactions from '../views/Transactions.vue';
import Accounts from '../views/Accounts.vue';
import AccountDetails from '../views/AccountDetails.vue';

import Layout from '../components/Layout.vue';

const routes = [
  {
    path: '/',
    redirect: '/dashboard',
    component: Layout,
    children: [
      { path: '/dashboard', name: 'Dashboard', component: Dashboard },
      { path: '/transactions', name: 'Transactions', component: Transactions },
      { path: '/accounts', name: 'Accounts', component: Accounts },
      { path: '/accounts/:bank_id/:account_id', name: 'AccountDetails', component: AccountDetails, props: true },
    ]
  },
  {
    path: '/account/add',
    name: 'AddAccount',
    component: AddAccount
  },

];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;