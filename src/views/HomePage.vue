<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <div id="main-layout">
        <div id="pizza-list">
          <div
            v-for="product in products"
            :key="product.id"
            class="product-item"
          >
            <ion-card style="flex: 3">
              <ion-card-header>
                <ion-card-title>{{ product.name }}</ion-card-title>
                <ion-card-subtitle>{{ product.description }}</ion-card-subtitle>
              </ion-card-header>
              <ion-card-content class="card-price">
                {{ Number(product.price).toFixed(2) }} €
              </ion-card-content>
              <ion-button
                style="margin-left: 10px; margin-bottom: 10px"
                @click="addToCart(product)"
                >Hinzufügen</ion-button
              >
            </ion-card>
            <img
              :alt="product.name"
              :src="`/img/${product.name}.png`"
              style="flex: 1"
              width="150px"
              @error="setDefaultImage"
            />
          </div>
        </div>

        <div id="shopping-cart">
          <ion-title id="cart-title">Deine Bestellung</ion-title>
          <div v-if="itemsInCart">
            <ion-list lines="none">
              <ion-item
                v-for="(productData, productName) in cart"
                :key="productName"
              >
                <ion-grid>
                  <ion-row>
                    <ion-col>
                      <ion-label>{{ productData.amount }}x</ion-label>
                    </ion-col>
                    <ion-col>
                      <ion-label>{{ productName }}</ion-label>
                    </ion-col>
                    <ion-col size="auto">
                      <ion-button
                        shape="round"
                        @click="increaseAmount(productName)"
                      >
                        <ion-icon slot="icon-only" :icon="add"></ion-icon>
                      </ion-button>
                      <ion-button
                        shape="round"
                        @click="removeFromCart(1, productName)"
                      >
                        <ion-icon slot="icon-only" :icon="remove"></ion-icon>
                      </ion-button>
                    </ion-col>
                    <ion-col size="auto">
                      <ion-label
                        >{{
                          getProductPrice(productName).toFixed(2)
                        }}
                        €</ion-label
                      >
                    </ion-col>
                    <ion-col size="auto">
                      <ion-button
                        @click="removeFromCart(productData.amount, productName)"
                        shape="round"
                        fill="clear"
                      >
                        <ion-icon :icon="trashOutline"></ion-icon>
                      </ion-button>
                    </ion-col>
                  </ion-row>
                </ion-grid>
              </ion-item>
            </ion-list>
            <ion-row>
              <ion-col size="7">
                <ion-label>Gesamt: </ion-label>
              </ion-col>
              <ion-col size="4">
                <ion-label>{{ totalPrice.toFixed(2) }} €</ion-label>
              </ion-col>
            </ion-row>
          </div>

          <div v-else id="empty-cart">
            <ion-icon :icon="cartOutline" style="font-size: 100px"></ion-icon>
            <ion-label
              >Wähle eine Pizza aus der Karte und bestell sie!</ion-label
            >
          </div>
          <ion-button
            expand="block"
            :disabled="!itemsInCart"
            @click="goto('/payment')"
            >Bestellen</ion-button
          >
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import {
  IonContent,
  IonPage,
  IonButton,
  IonList,
  IonItem,
  IonLabel,
  IonGrid,
  IonRow,
  IonCol,
  IonIcon,
  IonTitle,
  IonCard,
  IonCardContent,
  IonCardHeader,
  IonCardTitle,
  IonCardSubtitle,
} from "@ionic/vue";
import axios from "axios";
import { ref, onMounted } from "vue";
import { cartOutline, add, remove, trashOutline } from "ionicons/icons";
import { useRouter } from "vue-router";

const router = useRouter();

const goto = (path) => {
  localStorage.setItem("cart", JSON.stringify(cart.value));
  localStorage.setItem("prices", JSON.stringify(priceJSON.value));
  router.push(path);
};

const products = ref([]);
const cart = ref({});
const totalPrice = ref(0);
const itemsInCart = ref(false);
const priceJSON = ref({ total: 0 });

/**
 * Calls the 'api.php' script to get all products stored in the db.
 */
const fetchProducts = async () => {
  try {
    const response = await axios.get(
      "http://localhost/ionic-pizzawebsite/api.php"
    );
    products.value = response.data;
  } catch (error) {
    console.error("Fehler beim Abrufen der Produkte:", error);
  }
};

/**
 * Gets called if an image that should be loaded doesn't exist.
 * @param event comes from '@error' event
 */
const setDefaultImage = (event) => {
  event.target.src = "https://ionicframework.com/docs/img/demos/card-media.png";
};

/**
 * Add a pizza to the cart on the right side of the screen.
 * @param product JSON String from db with all information regarding the product
 */
const addToCart = (product) => {
  itemsInCart.value = true;

  // Check if the product is already in the cart
  if (cart.value[product.name]) {
    cart.value[product.name].amount += 1;
  } else {
    cart.value[product.name] = {
      id: products.value.find((p) => p.name === product.name).id,
      amount: 1,
    };
  }
  calculateTotalPrice();
};

/**
 * Calculates the prize of all products in the cart with the same name.
 * @param productName Name of the pizza
 */
const getProductPrice = (productName) => {
  const product = products.value.find((p) => p.name === productName); // Find the product by name
  if (product) {
    const price = product.price * cart.value[productName].amount; // price * amount
    priceJSON.value[productName] = price;
    return price;
  }
  return 0;
};

/**
 * Removes a proudct from the Cart.
 * @param amount How many times the item should be removed
 * @param productName Name of the product you want to reduce
 */
const removeFromCart = (amount, productName) => {
  //Cheks if the product ist in the cart
  if (!cart.value || !cart.value[productName]) {
    console.warn(
      `Produkt "${productName}" ist nicht im Warenkorb oder cart ist undefined.`
    );
    return;
  }

  //Reduces the value by amount and if the value is <= 0 deletes the key from cart
  cart.value[productName].amount -= amount;
  if (cart.value[productName].amount <= 0) {
    delete cart.value[productName];
  }

  calculateTotalPrice();

  //Checks if cart is empty
  if (Object.keys(cart.value).length === 0) {
    itemsInCart.value = false;
  }
};

/**
 * Increases the amount the product by one.
 * @param productName Name of the product which will be increased
 */
const increaseAmount = (productName) => {
  cart.value[productName].amount += 1;
  calculateTotalPrice();
};

/**
 * Calculates the total, combined price of all products currently in the cart.
 */
const calculateTotalPrice = () => {
  totalPrice.value = 0;
  for (const name of Object.keys(cart.value)) {
    totalPrice.value += getProductPrice(name);
    priceJSON.value["total"] = totalPrice.value;
  }
};

onMounted(fetchProducts);
</script>

<style scoped>
#main-layout {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
}

#pizza-list {
  flex: 3;
}

.product-item {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding: 16px;
  border-bottom: 1px solid #ccc;
}

.product-card {
  flex: 3;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.product-image {
  flex: 1;
  max-width: 150px;
  height: auto;
  object-fit: contain;
  margin-left: 16px;
}

.card-price {
  font-size: 20px;
  color: black;
}

#shopping-cart {
  flex: 1;
  align-items: center;
  gap: 20px;
  padding: 20px;
  background: #f8f8f8;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

#cart-title {
  text-align: center;
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 10px;
}

#empty-cart {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  gap: 20px;
}

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

@media (prefers-color-scheme: dark) {
  #shopping-cart {
    background-color: #333;
    color: white;
  }

  ion-label {
    color: white;
  }

  ion-button {
    --background: #1e1e23;
    --color: white;
  }

  #pizza-list {
    background-color: #1e1e1e;
  }

  .product-item {
    border-bottom: 1px solid #444;
  }
  
  .card-price {
    color: #EEEEEE;
  }
}
</style>
