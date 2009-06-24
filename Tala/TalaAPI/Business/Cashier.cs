using System;
using System.Collections.Generic;
using System.Linq;


namespace TalaAPI.Business
{
    public class Cashier
    {
        public static int CHIP_AN_CHOT = 4; /*số chip người bị ăn chốt phải trả cho người ăn chốt*/
        public static int CHIP_MOM = 4; /*số chip người móm phải trả cho người nhất*/
        public static int CHIP_U = 5; /*số chip người ù ăn được của mỗi người chơi*/
        public static int CHIP_U_TRON = 10; /*số chip người ù tròn ăn được của mỗi người chơi*/

        public static int CHIP_DEN = 5; /*số chip phải đền cho mỗi người chơi*/
        public static int CHIP_DEN_U_TRON = 10; /*số chip phải đền cho mỗi người chơi khi bi thang u tròn an 3 cay*/

        public static int CHIP_NOP_GA = 1; /*số chip phải nộp vào gà với mỗi lần bị ăn*/

        public static int CHIP_BET = 3; /*số chip người bét phải nộp cho người nhất*/
        public static int CHIP_BA = 2; /*số chip người ba phải nộp cho người nhất*/
        public static int CHIP_NHI = 1; /*số chip người nhì phải nộp cho người nhất*/

    }
}
