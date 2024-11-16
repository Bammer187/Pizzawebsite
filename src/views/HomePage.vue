<template>
  <ion-page>
    <ion-content :fullscreen="true">

      <div id="main-layout">
        <div class="pizza-list">
          <div v-for="product in products" :key="product.id" class="product-item">
            <h2>{{ product.name }}</h2>
            <p>{{ product.description }}</p>
            <p>{{ product.price }} €</p>
          </div>
        </div>

        <div id="shopping-cart">

        </div>
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup>
import { IonContent, IonPage } from '@ionic/vue';
import axios from 'axios';
import { ref, onMounted } from 'vue';

const products = ref([]);

const fetchProducts = async () => {
  try {
    const response = await axios.get('http://localhost/ionic-pizzawebsite/api.php');
    products.value = response.data;
    console.log('Produkte geladen:', products.value);
  } catch (error) {
    console.error('Fehler beim Abrufen der Benutzer:', error);
  }
}

onMounted(fetchProducts);
</script>

<style scoped>
</style>
