import { createRouter, createWebHistory } from 'vue-router'

import About from '@/views/AboutPage.vue'
import ArticleCatch from '@/views/ArticleCatchPage.vue'
import Login from '@/views/LoginPage.vue'
import Publish from '@/views/ArticleEntryPage.vue'
import MyPage from '@/views/ProfileEditPage.vue'
import Edit from '@/views/ArticleEditPage.vue'
import Search from '@/views/SearchPage.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/0', // 最初にアクセスしたときに、IDが0のTodoリストにリダイレクトする
    },
    {
      path: '/:id',
      name: 'mainTodo',
      component: () => import('@/views/MainTodo.vue'),
    },
    {
      path: '/publish',
      name: 'publish',
      component: Publish,
    },
    {
      path: '/about',
      redirect: '/about/0', // 最初にアクセスしたときに、IDが0のAboutPageにリダイレクトする
    },
    {
      path: '/about/:id',
      name: 'about',
      component: About,
    },
    {
      path: '/article',
      name: 'article',
      component: ArticleCatch,
    },
    {
      path: '/article/:id',
      name: 'articlePage',
      component: () => import('@/views/ArticlePage.vue'),
    },
    {
      path: '/test',
      name: 'testPage',
      component: MyPage,
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: { showHeader: false, showProfile: false },
    },
    {
      path: '/edit',
      redirect: '/edit/0', // 最初にアクセスしたときに、IDが0のAboutPageにリダイレクトする
    },
    {
      path: '/edit/:id',
      name: 'edit',
      component: Edit,
    },
    {
      path: '/tags/:word',
      name: 'search',
      component: Search,
    },
  ],
})

export default router
