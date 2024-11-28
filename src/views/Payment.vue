<template>
  <div id="main-layout">
    <div id="payment-information">
      <PaymentInformation v-model:inputFields="inputFields"/>
      <ion-button @click="confirmOrder"></ion-button>
    </div>
    <div id="shopping-cart" class="shopping-cart">
      <ion-title>Deine Bestellung</ion-title>
      <ion-list lines="none">
        <ion-item v-for="(quantity, productName) in cart" :key="productName">
          <ion-grid>
            <ion-row>
              <ion-col size="3">
                <ion-label>{{ quantity }}x</ion-label>
              </ion-col>
              <ion-col size="3">
                <ion-label>{{ productName }}</ion-label>
              </ion-col>  
              <ion-col size="3">
                <ion-label>{{ prices[productName].toFixed(2) }} €</ion-label>
              </ion-col>
            </ion-row>
          </ion-grid>
        </ion-item>
      </ion-list>
      <ion-label>Gesamt: {{ prices["total"].toFixed(2) }} €</ion-label>
    </div>
  </div> 
</template>

<script setup>
  import { IonList, IonItem, IonGrid, IonRow, IonCol, IonLabel, IonButton, IonTitle } from '@ionic/vue';
  import { ref, onMounted } from 'vue';
  import PaymentInformation from '../../components/PaymentInformation.vue';
  import axios from 'axios';

  const cart = ref({});
  const prices = ref({ "total": 0 });
  const order = ref({
    "total": 0,
    "date": '',
    "CustomerInfo": {},
    "pizzas": {},
  });

  const inputFields = ref({
    Adresse: '',
    Postleitzahl: '',
    Stadt: '',
    Vorname: '',
    Nachname: '',
    Telefon: '',
    Email: '',
  });

  const sendDataToDatabase = async (data) => {
    try {
        const response = await axios.post(
            'http://localhost/ionic-pizzawebsite/sendData.php',
            JSON.stringify(data),
            {
                headers: {
                    'Content-Type': 'application/json'
                }
            }
        );
    } catch (error) {
        console.error('Es gab einen Fehler:', error.message);
    }
};


  const confirmOrder = () => {
    order.value["pizzas"] = JSON.parse(JSON.stringify(cart.value));
    order.value["total"] = prices.value["total"];
    order.value["CustomerInfo"] = inputFields.value;

    const date = new Date();
    const currentDate = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate() + ' '
                      + date.getHours() + ':' + date.getMinutes() + ":" + date.getSeconds();
    order.value["date"] = currentDate;

    sendDataToDatabase(order.value);
  }

  onMounted(() => {
      const cartData = localStorage.getItem('cart');
      const pricesData = localStorage.getItem('prices');

      cart.value = cartData ? JSON.parse(cartData) : {};
      prices.value = pricesData ? JSON.parse(pricesData) : {"total": 0};
  });
</script>

<style scoped>
  #main-layout {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    margin-top: 20px
  }

  .payment-information {
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

    .payment-information {
      background-color: #1e1e1e;
    }
  }
</style>