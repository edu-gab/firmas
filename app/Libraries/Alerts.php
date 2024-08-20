<?php

namespace App\Libraries;

class Alerts
{
    /**
     * Mensaje que se va a mostar en el Alert
     *
     * @var string
     * */
    protected string $msg;

    /**
     * Tipo de mensaje que se va a mostrar
     *
     * @var string
     */
    protected string $type;

    /**
     * Titulo del Alert
     *
     * @var string
     * */
    protected string $title;

    /**
     * Arreglo con los colores que se pueden usar en los Alert
     *
     * @var array
     * */

    private array $colors =
        [
            'black'                 =>	'#000000',
            'silver'                =>  '#c0c0c0',
            'gray'                  =>	'#808080',
            'white'                 =>	'#ffffff',
            'maroon'	            =>  '#800000',
            'red'	                =>  '#ff0000',
            'purple'	            =>  '#800080',
            'fuchsia'	            =>  '#ff00ff',
            'green'	                =>  '#008000',
            'lime'	                =>  '#00ff00',
            'olive'	                =>  '#808000',
            'yellow'	            =>  '#ffff00',
            'navy'	                =>  '#000080',
            'blue'	                =>  '#0000ff',
            'teal'	                =>  '#008080',
            'aqua'	                =>  '#00ffff',
            'orange'    	        =>  '#ffa500',
            'aliceblue'	            =>  '#f0f8ff',
            'antiquewhite'	        =>  '#faebd7',
            'aquamarine'	        =>  '#7fffd4',
            'azure'	                =>  '#f0ffff',
            'beige'	                =>  '#f5f5dc',
            'bisque'	            =>  '#ffe4c4',
            'blanchedalmond'	    =>  '#ffe4c4',
            'blueviolet'	        =>  '#8a2be2',
            'brown'	                =>  '#a52a2a',
            'burlywood'	            =>  '#deb887',
            'cadetblue'	            =>  '#5f9ea0',
            'chartreuse'	        =>  '#7fff00',
            'chocolate'	            =>  '#d2691e',
            'coral'	                =>  '#ff7f50',
            'cornflowerblue'	    =>  '#6495ed',
            'cornsilk'	            =>  '#fff8dc',
            'crimson'	            =>  '#dc143c',
            'darkblue'	            =>  '#00008b',
            'darkcyan'	            =>  '#008b8b',
            'darkgoldenrod'	        =>  '#b8860b',
            'darkgray'	            =>  '#a9a9a9',
            'darkgreen'	            =>  '#006400',
            'darkgrey'	            =>  '#a9a9a9',
            'darkkhaki'	            =>  '#bdb76b',
            'darkmagenta'	        =>  '#8b008b',
            'darkolivegreen'	    =>  '#556b2f',
            'darkorange'	        =>  '#ff8c00',
            'darkorchid'	        =>  '#9932cc',
            'darkred'	            =>  '#8b0000',
            'darksalmon'	        =>  '#e9967a',
            'darkseagreen'	        =>  '#8fbc8f',
            'darkslateblue'	        =>  '#483d8b',
            'darkslategray'	        =>  '#2f4f4f',
            'darkslategrey'	        =>  '#2f4f4f',
            'darkturquoise'	        =>  '#00ced1',
            'darkviolet'	        =>  '#9400d3',
            'deeppink'	            =>  '#ff1493',
            'deepskyblue'	        =>  '#00bfff',
            'dimgray'	            =>  '#696969',
            'dimgrey'	            =>  '#696969',
            'dodgerblue'	        =>  '#1e90ff',
            'firebrick'	            =>  '#b22222',
            'floralwhite'	        =>  '#fffaf0',
            'forestgreen'	        =>  '#228b22',
            'gainsboro'	            =>  '#dcdcdc',
            'ghostwhite'	        =>  '#f8f8ff',
            'gold'	                =>  '#ffd700',
            'goldenrod'	            =>  '#daa520',
            'greenyellow'	        =>  '#adff2f',
            'grey'	                =>  '#808080',
            'honeydew'	            =>  '#f0fff0',
            'hotpink'	            =>  '#ff69b4',
            'indianred'	            =>  '#cd5c5c',
            'indigo'	            =>  '#4b0082',
            'ivory'	                =>  '#fffff0',
            'khaki'	                =>  '#f0e68c',
            'lavender'	            =>  '#e6e6fa',
            'lavenderblush'	        =>  '#fff0f5',
            'lawngreen'	            =>  '#7cfc00',
            'lemonchiffon'	        =>  '#fffacd',
            'lightblue'	            =>  '#add8e6',
            'lightcoral'	        =>  '#f08080',
            'lightcyan'	            =>  '#e0ffff',
            'lightgoldenrodyellow'  =>  '#fafad2',
            'lightgray'	            =>  '#d3d3d3',
            'lightgreen'	        =>  '#90ee90',
            'lightgrey'	            =>  '#d3d3d3',
            'lightpink'	            =>  '#ffb6c1',
            'lightsalmon'	        =>  '#ffa07a',
            'lightseagreen'	        =>  '#20b2aa',
            'lightskyblue'	        =>  '#87cefa',
            'lightslategray'	    =>  '#778899',
            'lightslategrey'	    =>  '#778899',
            'lightsteelblue'	    =>  '#b0c4de',
            'lightyellow'	        =>  '#ffffe0',
            'limegreen'	            =>  '#32cd32',
            'linen'	                =>  '#faf0e6',
            'mediumaquamarine'	    =>  '#66cdaa',
            'mediumblue'	        =>  '#0000cd',
            'mediumorchid'	        =>  '#ba55d3',
            'mediumpurple'	        =>  '#9370db',
            'mediumseagreen'	    =>  '#3cb371',
            'mediumslateblue'	    =>  '#7b68ee',
            'mediumspringgreen'	    =>  '#00fa9a',
            'mediumturquoise'	    =>  '#48d1cc',
            'mediumvioletred'	    =>  '#c71585',
            'midnightblue'	        =>  '#191970',
            'mintcream'	            =>  '#f5fffa',
            'mistyrose'	            =>  '#ffe4e1',
            'moccasin'	            =>  '#ffe4b5',
            'navajowhite'	        =>  '#ffdead',
            'oldlace'	            =>  '#fdf5e6',
            'olivedrab'	            =>  '#6b8e23',
            'orangered'	            =>  '#ff4500',
            'orchid'	            =>  '#da70d6',
            'palegoldenrod'	        =>  '#eee8aa',
            'palegreen'	            =>  '#98fb98',
            'paleturquoise'	        =>  '#afeeee',
            'palevioletred'	        =>  '#db7093',
            'papayawhip'	        =>  '#ffefd5',
            'peachpuff'	            =>  '#ffdab9',
            'peru'	                =>  '#cd853f',
            'pink'	                =>  '#ffc0cb',
            'plum'	                =>  '#dda0dd',
            'powderblue'	        =>  '#b0e0e6',
            'rosybrown'	            =>  '#bc8f8f',
            'royalblue'	            =>  '#4169e1',
            'saddlebrown'	        =>  '#8b4513',
            'salmon'	            =>  '#fa8072',
            'sandybrown'	        =>  '#f4a460',
            'seagreen'	            =>  '#2e8b57',
            'seashell'	            =>  '#fff5ee',
            'sienna'	            =>  '#a0522d',
            'skyblue'	            =>  '#87ceeb',
            'slateblue'	            =>  '#6a5acd',
            'slategray'	            =>  '#708090',
            'slategrey'	            =>  '#708090',
            'snow'	                =>  '#fffafa',
            'springgreen'	        =>  '#00ff7f',
            'steelblue'	            =>  '#4682b4',
            'tan'	                =>  '#d2b48c',
            'thistle'	            =>  '#d8bfd8',
            'tomato'	            =>  '#ff6347',
            'turquoise'	            =>  '#40e0d0',
            'violet'	            =>  '#ee82ee',
            'wheat'	                =>  '#f5deb3',
            'whitesmoke'	        =>  '#f5f5f5',
            'yellowgreen'	        =>  '#9acd32',
            'rebeccapurple'	        =>  '#663399'
        ];

    /** (Default) Show Alert
     * Función que permite mostrar una Alert por pantalla
     * @param string $msg mensaje a mostrar por pantalla
     * @param string $title titulo del Alert
     * @param string $type tipo de Alert
     */
    public function showAlert(string $msg, string $type, string $title): string
    {
        return "<script>
                    swal.fire({
                        title: '$title',
                        text: '$msg',
                        icon: '$type',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    })
                  </script>";
    }

    /**
     * Permite mostrar una alerta de error por pantalla
     * @param string $msg Mensaje a mostrar por pantalla
     * @param string $title Titulo del Alert
     * @param string $btnTxt Texto del botón
     * @param string $btnColor Color del botón
     *
     * @return string
     */
    public function showErrorAlert(string $msg, string $title, string $btnTxt = 'OK', string $btnColor = '#3085d6')
    {
        return "<script>
                    swal.fire({
                        title: '$title',
                        text: '$msg',
                        icon: 'error',
                        confirmButtonText: '$btnTxt',
                        confirmButtonColor: '$btnColor'
                    })
                  </script>";

    }

    /**
     * Permite mostrar una alerta de error por pantalla
     * @param string $msg Mensaje a mostrar por pantalla
     * @param string $title Titulo del Alert
     * @param string $btnTxt Texto del botón
     * @param string $btnColor Color del botón
     *
     * @return string
     */
    public function showInfoAlert(string $msg, string $title, string $btnTxt = 'OK', string $btnColor = '#3085d6')
    {
        return "<script>
                    swal.fire({
                        title: '$title',
                        text: '$msg',
                        icon: 'warning',
                        confirmButtonText: '$btnTxt',
                        confirmButtonColor: '$btnColor'
                    })
                  </script>";
    }

    /**
     * Permite mostrar una alerta de error por pantalla
     * @param string $msg Mensaje a mostrar por pantalla
     * @param string $title Titulo del Alert
     * @param string $btnTxt Texto del botón
     * @param string $btnColor Color del botón
     *
     * @return string
     */
    public function showQuestionAlert(string $msg, string $title, string $btnTxt = 'OK', string $btnColor = '#3085d6')
    {
        return "<script>
                    swal.fire({
                        title: '$title',
                        text: '$msg',
                        icon: 'warning',
                        confirmButtonText: '$btnTxt',
                        confirmButtonColor: '$btnColor'
                    })
                  </script>";
    }
}