<script setup>
import Layout from './Layout/Main.vue'
import { useForm, usePage} from '@inertiajs/vue3'
import { useTemplateRef } from "vue";

defineOptions({ layout: Layout })

const page = usePage();

const form = useForm({
    _token: page.props.csrf_token,
    uname: null,
    email: null,
    name: null,
    category: null,
    description: null,
    image: null,
    productDetails: 1,
    date_time: null,
    errorImage: null,
})

const myStepper = useTemplateRef('stepperActions')
    
function submit(){
    form.post('/post-product', {
        preserveScroll: true,
        onSuccess: () => {
            if(form.productDetails == 1){
                myStepper.value.next();
                form.productDetails += 1;
                form.errorImage = null;
            }else if(form.productDetails == 2){  
                myStepper.value.next();
                form.productDetails += 1;
                form.errorImage = null;
            }
              
        },
        onError: (errorImage) => {
            if(!form.errors.image){
                form.errorImage = errorImage;
            }
            
        }
    })
}

</script>

<template>
    <v-card>
        <v-container fluid>
            <v-stepper ref="stepperActions" :items="['Step 1', 'Step 2', 'Step 3']" hide-actions>
                <template v-slot:item.1>
                    <v-card title="Product" subtitle="Product Details" flat>
                        <v-form @submit.prevent="submit">
                            <v-card-text>
                                <v-text-field 
                                    v-model="form.name" 
                                    label="Product Name"
                                    class="ma-2 pa-2"
                                    :error-messages="form.errors.name"
                                    :counter="20"
                                    variant="outlined"
                                ></v-text-field>
                                <v-text-field
                                    v-model="form.category"
                                    label="Category"
                                    class="ma-2 pa-2"
                                    :error-messages="form.errors.category"
                                    variant="outlined"
                                ></v-text-field>
                                <v-textarea 
                                    v-model="form.description" 
                                    label="Description" 
                                    class="ma-2 pa-2"
                                    :error-messages="form.errors.description"
                                    :counter="1000"
                                    variant="outlined"
                                ></v-textarea>
                            </v-card-text>
                            <v-card-actions class="d-flex justify-end">
                                <v-btn color="primary" type="submit">
                                    Next
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </template>

                <template v-slot:item.2>
                    <v-card title="User" subtitle="User details" flat>
                        <v-container fluid>
                            <v-alert type="error" title="Error" v-if="form.errorImage">
                                    Please upload image only.
                            </v-alert>   
                            <v-form @submit.prevent="submit">
                                <v-card-text>
                                    <v-text-field
                                    v-model="form.uname"
                                    class="ma-2 pa-2"
                                    :counter="10"
                                    label="Name"
                                    variant="outlined"
                                    :error-messages="form.errors.uname"
                                    ></v-text-field>

                                    <v-text-field
                                    v-model="form.email"
                                    class="ma-2 pa-2"
                                    label="E-mail"
                                    variant="outlined"
                                    :error-messages="form.errors.email"
                                    ></v-text-field>

                                    <v-file-input
                                    v-model="form.image"
                                    class="ma-2 pa-2"
                                    label="Browse files..."
                                    multiple
                                    :error-messages="form.errors.image"
                                    variant="outlined"
                                    prepend-icon="mdi-camera"
                                    ></v-file-input>
                                </v-card-text>
                                <v-card-actions class="d-flex justify-end">
                                    <v-btn 
                                    color="primary"
                                    @click="$refs.stepperActions.prev(); form.productDetails = 1">
                                        Previous
                                    </v-btn>
                                    <v-btn 
                                        color="primary"
                                        type="submit"
                                        >
                                        Next
                                    </v-btn>
                                </v-card-actions>
                            </v-form>
                        </v-container>
                    </v-card>
                </template>

                <template v-slot:item.3>
                    <v-card title="Date" subtitle="Date & Time picker" flat>
                        <v-form @submit.prevent="submit">
                            <v-card-text>
                                <label class="form-label required fw-bold">Date Picker</label>
                                <v-text-field 
                                v-model="form.date_time" 
                                type="datetime-local" 
                                name="date" 
                                id="date"
                                :error-messages="form.errors.date_time"
                                width="250"

                            ></v-text-field>
                            </v-card-text>
                            <v-card-actions class="d-flex justify-end">
                                <v-btn 
                                color="primary"
                                @click="$refs.stepperActions.prev(); form.productDetails = 2">
                                    Previous
                                </v-btn>
                                <v-btn 
                                    color="primary"
                                    type="submit"
                                    >
                                    Submit
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </template>
            </v-stepper>
        </v-container>
    </v-card>
</template>


