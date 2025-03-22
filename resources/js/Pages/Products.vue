<script setup>
import Layout from './Layout/Main.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

defineOptions({ layout: Layout })

const props = defineProps({ products: Object, category: Object })

let dialog = ref(false)

const form = useForm({
    id: null,
    name: null,
    category: null,
    description: null,
    date_time: null,
    filter: null,
})

let search = ref(null)

function openDialog(data) {
    this.dialog = true;
    form.id = data.id
    form.name = data.name;
    form.category = data.category;
    form.description = data.description;
    form.date_time = data.date_time;
}

function update(){
    form.put('/update-product',{
        onSuccess: () => {
            alert("Success");
            this.dialog = false;
        }
    })
}

function remove(id){
    if (confirm("Do you want to remove this schedule?") == true) {
        router.delete('/delete-product/' + id, {
            onSuccess: () => {
            },
            onError: (error) => {
                console.log(error)
            }
        });
    } 
}

function paginations(){
    router.visit(props.products.links[props.products.current_page].url,
        { preserveState: true }
    )
    
}
function applyFilter(){
    router.get('/products',
        { 
            filter: form.filter,
            search: search.value
        },
        { preserveState: true }
    )
}


watch(search, (newSearch) => {
    router.get('/products',
        {
            search: newSearch,
            filter: 'Music',
        },
        {
            preserveState: true
        }
    )
})
console.log(props.category)
</script>
<template>
    <div class="d-flex justify-center">
        <v-card class="w-100">
           <v-container fluid>
                <v-card-title>
                    Products
                </v-card-title>
                <v-card-text>
                    <div class="container">
                        <v-btn color="success" href="/create-product" class="mb-5" prepend-icon="mdi-plus-circle">Create</v-btn>
                            
                            <v-row class="justify-space-between">
                                <v-col
                                    cols="12"
                                    md="5"
                                    sm="6"
                                >
                                    <v-text-field
                                        v-model="search"
                                        append-icon="search"
                                        label="Search"
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    md="3"
                                    sm="4"
                                >
                                    <v-form @submit.prevent="applyFilter()">
                                        <v-select 
                                            v-model="form.filter" 
                                            label="Filter"
                                            variant="outlined"
                                            :items="props.category"
                                            item-title="name"
                                            item-value="name"
                                            chips
                                        ></v-select>
                                        <div class="d-flex justify-end mb-2">
                                            <v-btn type="submit" class="mx-2">Apply Filters</v-btn>
                                            <v-btn href="/products">Remove Filters</v-btn>
                                        </div>
                                    </v-form>
                                </v-col>
                                
                            </v-row>
                    </div>
                    <v-table>
                        <thead>
                            <tr>
                                <th class="text-left">
                                    No.
                                </th>
                                <th class="text-left">
                                    Name
                                </th>
                                <th class="text-left">
                                    Category
                                </th>
                                <th class="text-left">
                                    Description
                                </th>
                                <th class="text-left">
                                    Date & Time
                                </th>
                                <th class="text-left">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item, key in products.data">
                                <td>{{ ++key }}</td>
                                <td>{{ item.name }}</td>
                                <td>{{ item.category }}</td>
                                <td>{{ item.description }}</td>
                                <td>{{ item.date_time }}</td>
                                <td>
                                    <v-btn icon="mdi-pencil" @click="openDialog(item)" class="ma-2 pa-2" size="x-small"></v-btn>
                                    <v-btn icon="mdi-trash-can-outline" @click="remove(item.id)" class="ma-2 pa-2" size="x-small"></v-btn>
                                </td>
                                
                            </tr>
        
                        </tbody>
                        
                    </v-table>
                    <div v-if="products.data.length == 0" class="text-center text-h3">No Item</div>
                    <v-pagination 
                        v-model="props.products.current_page" 
                        :length="props.products.last_page" 
                        @update:modelValue="paginations"
                    ></v-pagination>
                    
                    <v-dialog
                        v-model="dialog"
                        width="520px"
                    >
                        <v-card>
                            <v-container fluid>
                                    <v-card-title>
                                        Update Product
                                    </v-card-title>
                                    <v-form @submit.prevent="update()">
                                        <v-card-text>   
                                                <v-text-field 
                                                    v-model="form.name" 
                                                    label="Name"
                                                    :error-messages="$page.props.errors.name"
                                                    variant="outlined"
                                                ></v-text-field>
                                                <v-select
                                                    :items="items = 
                                                            [
                                                                'Category 1',
                                                                'Category 2',
                                                                'Category 3',
                                                                'Category 4',
                                                            ]"
                                                    v-model="form.category" 
                                                    label="Category"
                                                    :error-messages="$page.props.errors.category"
                                                    variant="outlined"
                                                ></v-select>
                                                <v-textarea
                                                    v-model="form.description" 
                                                    label="Description"
                                                    :error-messages="$page.props.errors.description"
                                                    variant="outlined"
                                                ></v-textarea>
                                                <v-text-field
                                                    v-model="form.date_time" 
                                                    type="datetime-local" 
                                                    label="Date & Time"
                                                    :error-messages="$page.props.errors.date_time"
                                                    variant="outlined"
                                                ></v-text-field>
                                            
                                        </v-card-text>
                                        <v-card-actions class="justify-end">
                                            <v-btn color="primary" @click="dialog = false">Close</v-btn>
                                            <v-btn type="submit">Update</v-btn>
                                        </v-card-actions>
                                    </v-form>
                            </v-container>
                        </v-card>
                    </v-dialog>
                </v-card-text>
           </v-container>
        </v-card>
    </div>
</template>