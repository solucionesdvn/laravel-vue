<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head>
    <title>Bienvenido | Soluciones Empresariales</title>
    <meta name="description" content="Sistema integral para la gestión de ventas, inventario, clientes, documentos, caja, gastos y más. Optimiza la administración de tu empresa con nuestras herramientas digitales." />
    <meta name="keywords" content="ventas, inventario, clientes, documentos, caja, proveedores, categorías, gestión empresarial, punto de venta, gastos, productos, reportes" />
    <meta name="robots" content="index, follow" />
    <link rel="canonical" href="https://tudominio.com/" />
  </Head>

  <div
    class="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]"
  >
    <!-- Header / Login / Dashboard -->
    <header class="mb-8 w-full max-w-4xl flex justify-end">
      <Link
        v-if="$page.props.auth.user"
        :href="route('dashboard')"
        class="transform rounded-sm border border-[#19140035] px-5 py-2 text-sm font-medium transition-transform hover:scale-105 hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
      >
        Dashboard
      </Link>
      <Link
        v-else
        :href="route('login')"
        class="ml-4 transform rounded-sm border border-transparent px-5 py-2 text-sm font-medium transition-transform hover:scale-105 hover:border-[#19140035] dark:hover:border-[#3E3E3A]"
      >
        Iniciar sesión
      </Link>
    </header>

    <!-- Main Card -->
    <main
      class="flex w-full max-w-4xl flex-col-reverse overflow-hidden rounded-lg lg:flex-row lg:shadow-xl lg:transition-transform lg:duration-500 lg:hover:scale-[1.02]"
    >
      <!-- Left Content -->
      <div
        class="flex-1 bg-white p-8 text-sm leading-6 text-[#1b1b18] dark:bg-[#161615] dark:text-[#EDEDEC] lg:p-16"
      >
        <h1 class="mb-4 text-2xl font-semibold">Bienvenido a Soluciones Empresariales</h1>
        <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">
          Descubre una plataforma completa para gestionar ventas, inventario, clientes, documentos, caja, gastos y mucho más. Todas las herramientas que tu empresa necesita en un solo lugar.
        </p>

        <!-- Lista de funcionalidades -->
        <ul class="mb-6 space-y-4">
          <li class="flex items-center gap-3">
            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#f53003] text-white font-bold">✓</span>
            <Link
              href="/sales"
              class="font-medium text-[#f53003] underline underline-offset-2 hover:text-[#ff3b1f] dark:text-[#FF4433] dark:hover:text-[#ff1f0f]"
            >
              Gestión de Ventas y Punto de Venta
            </Link>
          </li>
          <li class="flex items-center gap-3">
            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#f53003] text-white font-bold">✓</span>
            <Link
              href="/products"
              class="font-medium text-[#f53003] underline underline-offset-2 hover:text-[#ff3b1f] dark:text-[#FF4433] dark:hover:text-[#ff1f0f]"
            >
              Control de Inventario y Productos
            </Link>
          </li>
          <li class="flex items-center gap-3">
            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#f53003] text-white font-bold">✓</span>
            <Link
              href="/clients"
              class="font-medium text-[#f53003] underline underline-offset-2 hover:text-[#ff3b1f] dark:text-[#FF4433] dark:hover:text-[#ff1f0f]"
            >
              Gestión de Clientes
            </Link>
          </li>
          <li class="flex items-center gap-3">
            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#f53003] text-white font-bold">✓</span>
            <Link
              href="/categories"
              class="font-medium text-[#f53003] underline underline-offset-2 hover:text-[#ff3b1f] dark:text-[#FF4433] dark:hover:text-[#ff1f0f]"
            >
              Organización por Categorías
            </Link>
          </li>
          <li class="flex items-center gap-3">
            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#f53003] text-white font-bold">✓</span>
            <Link
              href="/suppliers"
              class="font-medium text-[#f53003] underline underline-offset-2 hover:text-[#ff3b1f] dark:text-[#FF4433] dark:hover:text-[#ff1f0f]"
            >
              Proveedores y Compras
            </Link>
          </li>
          <li class="flex items-center gap-3">
            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#f53003] text-white font-bold">✓</span>
            <Link
              href="/cash-registers"
              class="font-medium text-[#f53003] underline underline-offset-2 hover:text-[#ff3b1f] dark:text-[#FF4433] dark:hover:text-[#ff1f0f]"
            >
              Control de Caja y Movimientos
            </Link>
          </li>
          <li class="flex items-center gap-3">
            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#f53003] text-white font-bold">✓</span>
            <Link
              href="/expenses"
              class="font-medium text-[#f53003] underline underline-offset-2 hover:text-[#ff3b1f] dark:text-[#FF4433] dark:hover:text-[#ff1f0f]"
            >
              Registro de Gastos
            </Link>
          </li>
          <li class="flex items-center gap-3">
            <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#f53003] text-white font-bold">✓</span>
            <Link
              href="/document-templates"
              class="font-medium text-[#f53003] underline underline-offset-2 hover:text-[#ff3b1f] dark:text-[#FF4433] dark:hover:text-[#ff1f0f]"
            >
              Documentos y Formatos Personalizados
            </Link>
          </li>
        </ul>

        <!-- Call to action -->
        <div class="flex gap-4">
          <Link
            href="/contacto"
            class="inline-block rounded-sm border border-black bg-[#1b1b18] px-6 py-2 text-white transition-transform hover:scale-105 hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:text-[#1C1C1A]"
          >
            Contáctanos
          </Link>
        </div>
      </div>

      <!-- Right Image / SVG -->
      <div
        class="relative w-full overflow-hidden bg-[#fff2f2] dark:bg-[#1D0002] lg:w-[450px] flex justify-center items-center p-4"
      >
        <!-- SVG Animado -->
        <svg
          viewBox="0 0 438 104"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
          class="w-full max-w-xs animate-bounce-slow text-[#F53003] dark:text-[#F61500]"
        >
          <path d="M17.2 -3H0V102.2H49.5V86.7H17.2V-3Z" fill="currentColor"/>
          <path d="M438 -3H421.7V102.2H438V-3Z" fill="currentColor"/>
        </svg>
      </div>
    </main>
  </div>
</template>

<style>
@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
.animate-bounce-slow {
  animation: bounce-slow
}
</style>