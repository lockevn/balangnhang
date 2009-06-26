using System;
using System.Collections.Generic;
using System.Linq;
using Quantum.Tala.Service.Authentication;
using System.Data.Common;
using Quantum.Tala.Lib;


namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// Quầy thu ngân, tính cước của trò chơi. Tất cả các giao dịch dính đến TIỀN hay CHIP đều thực hiện ở đây
    /// </summary>
    public class Cashier
    {
        public const int CHIP_AN_CHOT = 4; /*số chip người bị ăn chốt phải trả cho người ăn chốt*/
        public const int CHIP_MOM = 4; /*số chip người móm phải trả cho người nhất*/
        public const int CHIP_U = 5; /*số chip người ù ăn được của mỗi người chơi*/
        public const int CHIP_U_TRON = 10; /*số chip người ù tròn ăn được của mỗi người chơi*/

        public const int CHIP_DEN = 5; /*số chip phải đền cho mỗi người chơi*/
        public const int CHIP_DEN_U_TRON = 10; /*số chip phải đền cho mỗi người chơi khi bi thang u tròn an 3 cay*/

        public const int CHIP_NOP_GA = 1; /*số chip phải nộp vào gà với mỗi lần bị ăn*/

        public const int CHIP_BET = 3; /*số chip người bét phải nộp cho người nhất*/
        public const int CHIP_BA = 2; /*số chip người ba phải nộp cho người nhất*/
        public const int CHIP_NHI = 1; /*số chip người nhì phải nộp cho người nhất*/



        /// <summary>
        /// Nộp 1 CHIP vào Gà của Sới, lấy TIỀN từ túi User phải nộp (có tính tỷ giá chip).
        /// Số chip cần nộp lấy trong thông số của Sới
        /// </summary>
        /// <param name="soi">Sới mà User đang chơi</param>
        /// <param name="userPhaiNop">Lấy từ túi người này để nộp</param>
        /// <returns>Số tiền đã nộp</returns>
        public static int NopGa(Soi soi, TalaUser userPhaiNop)
        {            
            int nTienPhat = Cashier.CHIP_NOP_GA * soi.SoiOption.TiGiaChip;            
            // trừ tiền trong túi user đi
            userPhaiNop.SubtractMoney(nTienPhat);

            // cộng gà lên 1 chip
            soi.GaValue += 1;

            return nTienPhat;
        }

        public static int SubtractMoneyOfUser(string p_sUsername, int p_nValue)
        {
            DbConnection con = DBHelper.Instance.GetDbConnection("quantum");
            con.Open();
            string strSQL = string.Format("update `User` set `balance` = ifnull(`balance`,0) - {1} where `u`='{0}';", p_sUsername, p_nValue);
            DbCommand command = con.CreateCommand();
            command.CommandText = strSQL;
            object oRet = command.ExecuteScalar();
            con.Close();

            int nRet = -1;
            int.TryParse(oRet as string, out nRet);
            return nRet;
        }

        public static int AddMoneyOfUser(string p_sUsername, int p_nValue)
        {
            DbConnection con = DBHelper.Instance.GetDbConnection("quantum");
            con.Open();
            string strSQL = string.Format("update `User` set `balance` = ifnull(`balance`,0) + {1} where `u`='{0}';", p_sUsername, p_nValue);
            DbCommand command = con.CreateCommand();
            command.CommandText = strSQL;
            object oRet = command.ExecuteScalar();
            con.Close();

            int nRet = -1;
            int.TryParse(oRet as string, out nRet);
            return nRet;
        }
    
    }
}
