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
          <h2>Dein Warenkorb</h2>

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
const cart = ref([]);

const fetchProducts = async () => {
  try {
    const response = await axios.get('http://localhost/ionic-pizzawebsite/api.php');
    products.value = response.data;
    console.log('Produkte geladen:', products.value);
  } catch (error) {
    console.error('Fehler beim Abrufen der Benutzer:', error);
  }
}

const addToCart = () => {

}

const removeFromCart = () => {

}

const increaseAmount = () => {
  
}

onMounted(fetchProducts);
</script>

<style scoped>
.main-layout {
  display: flex;
  flex-direction: row;
  gap: 20px;
  padding: 16px;
}

.pizza-list {
  flex: 2;
}

.shopping-cart {
  flex: 1;
  background: #f8f8f8;
  padding: 16px;
  border-radius: 8px;
}
</style>
