Vue.component("modal", {
    props: ["material_item"],
    methods: {
        toggleModal: function () {
            this.$emit("toggle-modal");
        },
    },
    computed: {
        custom_style: function () {
            return this.material_item.format === ".pdf" ? "height: 35em" : "";
        },
    },
    template: `
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container" :style="custom_style">
                    <div @click="toggleModal" class="modal-close"><span class="material-icons">cancel</span></div>
                    <video v-if="material_item.format === '.mp4'" width="100%" controls>
                        <source :src="material_item.link_file" type="video/mp4">
                        Tu navegador no soporta video en HTML.
                    </video>
                    <object v-if="material_item.format === '.pdf'" :data="material_item.link_file" type="application/pdf" width="100%" height="100%">
                        <p>En caso no se visualice el PDF has click <a :href="material_item.link_file">aqui!</a></p>
                    </object>
                </div>
            </div>
        </div>`,
});

var app = new Vue({
    el: "#app",
    delimiters: ["{(", ")}"],
    data: {
        showModal: false,
        material: [],
        material_item: {},
    },
    created() {
        this.getMateriales();
    },
    methods: {
        getMateriales: function () {
            let frm = new FormData();
            frm.append("request_type", "getMateriales");
            axios.post("api/ajax_controller.php", frm).then((res) => {
                this.material = res.data;
            });
        },
        toggleModal: function () {
            this.showModal = !this.showModal;
        },
        dynamicOptionCheckbox: function (id) {
            let frm = new FormData();
            frm.append("request_type", "materialesMarcadosByUser");
            frm.append("materialid", id);
            frm.append("sesskey", sesskey);
            axios.post("api/ajax_controller.php", frm).then((res) => {});
        },
        staticOptionCheckbox: function () {
            console.log(
                "aqui se ejecuta el metodo cuando se hace click en el checkbox de las opciones por defecto"
            );
        },
    },
});
