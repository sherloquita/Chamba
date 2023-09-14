axios.defaults.headers.common['token'] = GLOBAL_TOKEN;
axios.defaults.headers.common['sesion'] = GLOBAL_SESION;

Vue.filter('moneda', function (value) {
    return '$' + parseFloat(value).toFixed(2);
});

Vue.filter('porcentaje', function (value) {
    return parseFloat(value).toFixed(2) + '%';
});

var appSearch = new Vue({
    el: '#vue-search',
    data: {
        busqueda: "",
        total_de_resultados: "",
        almacen: "",
        todas_almacenes: []
    },
    mounted: function () {

    },
    methods: {
        ejecutar_busqueda: function () {
            var _url = new URL(window.location.href);
            _url.searchParams.set('b', this.busqueda);
            _url.searchParams.set('p', 1);
            _url.searchParams.set('c', this.almacen);
            window.location.href = _url;
        },
        show_modal_insertar: function () {
            appModal.limpiar_campos();
            appModal.opcion = "insertar";

            $("#modal-titulo").html('Agregar almacén');
            $("#vue-modal-insertar-editar").modal('show');
        }
    }
});

var appModal = new Vue({
    el: '#vue-modal-insertar-editar',
    data: {
        opcion:"",
        ID:"",
        CLAVE:"",
        NOMBRE_ALMACEN: "",
        DIRECCION_ALMACEN: "",
        TIPO_ALMACEN: "",
    },
    mounted: function() {

    },
    methods: {
        limpiar_campos: function (){
            this.ID = "";
            this.CLAVE = "";
            this.NOMBRE_ALMACEN = "";
            this.DIRECCION_ALMACEN = "";
            this.TIPO_ALMACEN = "";
        },
        validad_clave: function(event){
            this.CLAVE = this.CLAVE.toUpperCase();
            this.CLAVE = this.CLAVE.replace(/[^A-Z0-9]/g, '');
            this.CLAVE = this.CLAVE.slice(0, 3);
        },
        guardar: function(){
            let th = this;
            let send_data = {
                CLAVE: this.CLAVE,
                NOMBRE_ALMACEN: this.NOMBRE_ALMACEN,
                DIRECCION_ALMACEN: this.DIRECCION_ALMACEN,
                TIPO_ALMACEN: this.TIPO_ALMACEN,
            };
            if (th.opcion == "insertar"){
                axios.post(GLOBAL_APISERVER + '/private/kubotix/inventario/almacen/', send_data
                    ).then(function(response) {
                        console.log(response)
                        if(response.data.response === "success"){
                            Swal.fire({
                                title: "¡Excelente!",
                                text: "Registro insertado",
                                icon: "success",
                                comfirmButtonText: "aceptar"
                            }).then(result =>{
                                if (result.isConfirmed) {
                                    $("#vue-modal-insertar-editar").modal('hide');
                                    app.get_all_almacenes();
                                }
                            })
                        }
                })
                .catch(function(error){
                    console.log(error);
                });
            }
            else if (th.opcion == "editar"){
                send_data.ID = th.ID;
                axios.put(GLOBAL_APISERVER + '/private/kubotix/inventario/almacen', send_data
                    ).then(function(response){
                        console.log(response)
                        if(response.data.response === "success"){
                            Swal.fire({
                                title: "¡Excelente!",
                                text: "Registro insertado",
                                icon: "success",
                                comfirmButtonText: "aceptar"
                            }).then(result => {
                                if (result.isConfirmed){
                                    $("#vue-modal-insertar-editar").modal('hide');
                                    app.get_all_almacenes();
                                }
                            })
                        }
                    })
                    .catch(function(error){
                        console-log(error);
                    });
            }
        }
    }
});

var app = new Vue({
    el: '#vue-content',
    data: {
        almacenes: [],
        total_de_paginas:null,
        resultados_por_pagina:50,
        pagina_actual:global_pagina,
    },
    mounted: function(){
        this.get_all_almacenes();
        $("#kt_body").addClass("kt-primary--minimize aside-minimize");
    },
    methods: {
        show_modal_editar: async function (_id) {
            appModal.limpiar_campos();

            let response = await axios.get(GLOBAL_APISERVER + '/private/kubotix/inventario/almacen_by_id/?id='+_id);

            appModal.ID = response.data.data.ID;
            appModal.CLAVE = response.data.data.CLAVE;
            appModal.NOMBRE_ALMACEN = response.data.data.NOMBRE_ALMACEN;
            appModal.DIRECCION_ALMACEN = response.data.data.DIRECCION_ALMACEN;
            appModal.TIPO_ALMACEN = response.data.data.TIPO_ALMACEN;
            appModal.opcion = "editar";

            
            $("#modal-titulo").html('Editar almacén');
            $("#vue-modal-insertar.editar").modal('show');
        },
        get_all_almacenes: async function() {
            let th = this;
            
            if (global_busqueda == null){
                let response = await axios.get(GLOBAL_APISERVER + '/private/kubotix/inventario/all_almacenes/')
                th.almacenes = response.data.data;
                appSearch.total_de_resultados = response.data.total;
            }
            else{
                let response = await axios.get(GLOBAL_APISERVER + '/private/kubotix/inventario/all_almacenes/?b='+global_busqueda+'&p='+global_pagina_actual+'&c='+global_almacen)
                th.almacenes = response.data.data;
                appSearch.total_de_resultados = response.data.total;
            }
            th.total_de_paginas = Math.ceil(appSearch.total_de_resultados / th.resultados_por_pagina);
            console.log(th.total_de_paginas);
        },
        eliminar_registro: async function(_id) {
            try {
              const url = `/private/kubotix/inventario/eliminar_by_id/?id=${_id}`;
              const response = await axios.delete(url);
              
              if (response.status === 200) {
                // El registro se eliminó con éxito
                console.log('Registro eliminado con éxito');
                // Realiza cualquier otra acción que necesites después de la eliminación
              } else {
                console.error('Error al eliminar el registro:', response.statusText);
              }
            } catch (error) {
              console.error('Error al realizar la solicitud:', error);
            }
          },
          
        mover_pagina: function(_p){
            var _url = new URL(window.location.href);
            _url.searchParams.set('p', _p);
            window.location.href = _url;
        },
        format_date(_f){
            if (_f){
                return SVGAnimateMotionElement(String(_f)).format('DD/MM/YYYY')
            }
        },
        calcular_margen(costo, precio){
            return costo / precio * 100;
        }
    },
});