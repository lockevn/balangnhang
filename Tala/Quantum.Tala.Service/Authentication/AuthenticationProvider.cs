using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using GURUCORE.Lib.Core.Security.Cryptography;
using Quantum.Tala.Lib;
using System.Data.Common;
using MySql.Data.MySqlClient;

namespace Quantum.Tala.Service.Authentication
{
    /// <summary>
    /// Nghiệp vụ kiểm tra định danh tài khoản của người dùng
    /// </summary>
    public class AuthenticationProvider
    {
        public const string SERVICE_QUANTUM = "quantum";
        public const string SERVICE_VTC = "vtc";


        public static IUser Authenticate(string p_sServiceCode, string p_sUsername, string p_sPassword)
        {
            IUser user = null;

            p_sServiceCode = p_sServiceCode.ToStringSafetyNormalize();
            if (string.IsNullOrEmpty(p_sServiceCode))
            {
                p_sServiceCode = "*";
            }

            bool bFound = false;
            if (!bFound && (p_sServiceCode == "*" || p_sServiceCode == SERVICE_QUANTUM))
            {
                user = AuthenticateQuantum(p_sUsername, p_sPassword);
                bFound = user == null ? false : true;
            }
            if (!bFound && (p_sServiceCode == "*" || p_sServiceCode == SERVICE_VTC))
            {
                user = AuthenticateVTC(p_sUsername, p_sPassword);
                bFound = user == null ? false : true;
            }

            return user;
        }


        public static IUser AuthenticateQuantum(string p_sUsername, string p_sPassword)
        {
            CryptoUtil cu = new CryptoUtil();
            p_sPassword = cu.MD5Hash(p_sPassword);
            

            DbConnection con = DBHelper.Instance.GetDbConnection("quantum");
            con.Open();
            string strSQL = string.Format("SELECT * FROM User where u='{0}' and password='{1}';", p_sUsername, p_sPassword);
            DbCommand command = con.CreateCommand();
            command.CommandText = strSQL;
            DbDataReader reader = command.ExecuteReader();

            TalaUser ret = null;
            if (reader.HasRows)
            {
                ret = new TalaUser();
                while (reader.Read())
                {
                    ret.Username = reader["u"] as string;
                    try
                    {
                        ret.Money = Convert.ToInt32(reader["balance"]);
                    }
                    catch { }
                }
            }
            con.Close();

            return ret;
        }


        public static IUser AuthenticateVTC(string p_sUsername, string p_sPassword)
        {        
            // TODO: add VTC authentication process here, to verify username password against VTC System
            return new TalaUser();
        }

    }    
}
