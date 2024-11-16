<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <div id="main-layout">
        <div class="pizza-list">
          <div v-for="product in products" :key="product.id" class="product-item">
            <h2>{{ product.name }}</h2>
            <p>{{ product.description }}</p>
            <p>{{ product.price.toFixed(2) }} €</p>
            <ion-button @click="addToCart(product)">Hinzufügen</ion-button>
          </div>
        </div>

        <div id="shopping-cart" class="shopping-cart">
          <h2>Warenkorb</h2>
          <ion-list>
            <ion-item v-for="(quantity, productName) in cart" :key="productName">
              <ion-grid>
                <ion-row>
                  <ion-col size="4">
                    <ion-label>{{ quantity }}x</ion-label>
                  </ion-col>
                  <ion-col size="4">
                    <ion-label>{{ productName }}</ion-label>
                  </ion-col>
                  <ion-col size="4">
                    <ion-label>{{ getProductPrice(productName).toFixed(2) }} €</ion-label>
                  </ion-col>
                </ion-row>
              </ion-grid>
            </ion-item>
          </ion-list>
          <p>Gesamt: {{ totalPrice.toFixed(2) }} €</p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { IonContent, IonPage, IonButton, IonList, IonItem, IonLabel, IonGrid, IonRow, IonCol } from '@ionic/vue';
import axios from 'axios';
import { ref, onMounted } from 'vue';

const products = ref([]);
const cart = ref({});
const totalPrice = ref(0);

const fetchProducts = async () => {
  try {
    const response = await axios.get('http://localhost/ionic-pizzawebsite/api.php');
    products.value = response.data;
    console.log('Produkte geladen:', products.value);
  } catch (error) {
    console.error('Fehler beim Abrufen der Produkte:', error);
  }
}

/**
 * Add a pizza to the cart on the right side of the screen.
 * @param product JSON String from db with all information regarding the product
 */
const addToCart = (product) => {
  totalPrice.value += product.price;
  console.log(totalPrice.value);

  // Check if the product is already in the cart
  if (cart.value[product.name]) {
    cart.value[product.name] += 1;
  } else {
    cart.value[product.name] = 1;
  }

  console.log(cart.value);
}

/**
 * Calculates the prize of all products in the cart with the same name.
 * @param productName Name of the pizza
 */
const getProductPrice = (productName) => {
  const product = products.value.find(p => p.name === productName); // Find the product by name
  if (product) {
    return product.price * cart.value[productName]; // price * amount
  }
  return 0;
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
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

#shopping-cart ion-item {
  margin-bottom: 16px;
}

/* Standard Styles */
ion-label {
  font-size: 16px;
}

ion-col {
  display: flex;
  align-items: center;
}

#shopping-cart ion-grid {
  width: 100%;
}

#shopping-cart ion-col {
  margin-right: 16px; 
}

/* Dark Mode Styles */
@media (prefers-color-scheme: dark) {
  #shopping-cart {
    background-color: #333;
    color: white;
  }

  ion-label {
    color: white;
  }

  .pizza-list {
    background-color: #1e1e1e;
  }
}

#main-layout {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
}

.pizza-list {
  flex: 2;
}

.shopping-cart {
  flex: 1;
  background: #f8f8f8;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
</style>
