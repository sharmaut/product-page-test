import { createRouter, createWebHashHistory } from 'vue-router'
import Product from './views/Product.vue';
import NotFound from './views/NotFound.vue';

const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    {
      path: '/',
      redirect: () => ({ name: 'product', params: { slug: 'fall-limited-edition-sneakers' } })
    },
    {
      path: '/shop/product/:slug',
      name: 'product',
      component: Product
    },
    {
      path: '/:catchAll(.*)',
      name: 'NotFound',
      component: NotFound
    }
  ],
})

export default router;
