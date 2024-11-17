<template>
  <ion-app>
    <ion-router-outlet id="main-content" :key="$route.fullPath"/>
  </ion-app>
</template>

<script setup>
import { IonApp, IonRouterOutlet } from '@ionic/vue';
</script>
