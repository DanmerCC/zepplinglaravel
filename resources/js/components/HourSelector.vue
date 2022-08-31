<template>
    <select class="form-select" name="" id="" v-model="int_input">
        <option
            v-for="(option, index) in option_hora"
            :value="option"
            :key="index"
            aria-placeholder="Hora"
        >
            {{ option }}
        </option>
    </select>
</template>

<script>
export default {
    props: {
        value: {
            type: String,
            default: "13",
        },
    },
    watch: {
        int_input(newValue, oldValue) {
            this.$emit("input", newValue);
        },
    },
    mounted() {
        this.$emit("input", this.int_input);
    },
    data() {
        return {
            int_input: null,
            option_hora: [
                null,
                "01",
                "02",
                "03",
                "04",
                "05",
                "06",
                "07",
                "08",
                "09",
                "10",
                "11",
                "12",
                "13",
                "14",
                "15",
                "16",
                "17",
                "18",
                "19",
                "20",
                "21",
                "22",
                "23",
                "24",
            ],
        };
    },
};
</script>

<style></style>
