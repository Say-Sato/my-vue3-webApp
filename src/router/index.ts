import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/0', // 最初にアクセスしたときはID 0のTodoへ
    },
    {
      path: '/:id',
      name: 'mainTodo',
      component: () => import('@/views/MainTodo.vue'),
    },
    {
      path: '/publish',
      name: 'publish',
      component: () => import('@/views/ArticleEntryPage.vue'),
    },
    {
      path: '/article',
      name: 'article',
      component: () => import('@/views/ArticleCatchPage.vue'),
    },
    {
      path: '/article/:id',
      name: 'articlePage',
      component: () => import('@/views/ArticlePage.vue'),
    },
    {
      path: '/test',
      name: 'testPage',
      component: () => import('@/views/ProfileEditPage.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginPage.vue'),
      meta: {
        showHeader: false,
        showProfile: false, // ← ログイン時にヘッダーやプロフィール表示を制御したい場合
      },
    },
    {
      path: '/edit',
      redirect: '/edit/0', // ID指定なしはID 0を編集
    },
    {
      path: '/edit/:id',
      name: 'edit',
      component: () => import('@/views/ArticleEditPage.vue'),
    },
    {
      path: '/tags/:word',
      name: 'search',
      component: () => import('@/views/SearchPage.vue'),
    },
  ],
  scrollBehavior() {
    // 遷移後は必ず最上部へスクロール
    return { top: 0 }
  },
})

export default router
