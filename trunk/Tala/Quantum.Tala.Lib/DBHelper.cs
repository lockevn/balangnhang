using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Data.Common;
using MySql.Data.MySqlClient;
using System.IO;

namespace Quantum.Tala.Lib
{
    /// <summary>
    /// nạp file cấu hình kết nối tới DB, thao tác dữ liệu đơn giản với DB
    /// </summary>
    public class DBHelper
    {
        #region Singleton Implementation

        private static readonly DBHelper instance = new DBHelper();

        DBHelper()
        {
            /// THIS FUNCTION RUN BEFORE STATIC CONSTRUCTOR
            // Console.WriteLine("In the internal constructor");            
        }

        // Explicit static constructor to tell C# compiler
        // not to mark type as beforefieldinit
        static DBHelper()
        {
            // Console.WriteLine("In the static constructor");
        }


        /// <summary>
        /// The public Instance property to use
        /// </summary>
        public static DBHelper Instance
        {
            get { return instance; }
        }

        #endregion

        
        /// <summary>
        /// Nạp các dữ liệu từ db.conf để lấy các connectionString để sử dụng
        /// </summary>
        /// <param name="rootpath">Application root path, không nên để / ở cuối. Nếu là web, truyền mapPath("/"). Nếu là App, truyền Assembly.Location</param>
        public void Init(string rootpath)
        {
            // Nếu chưa nạp ConnectionString
            if (null == DicConnectionString)
            {
                DicConnectionString = new Dictionary<string, string>();
                // LOAD
                string[] arrStringConfig = File.ReadAllLines(rootpath + "/db.config");
                foreach (string s in arrStringConfig)
                {
                    string[] arrNameValuePair = s.Split(new char[] { '=' }, 2, StringSplitOptions.RemoveEmptyEntries);
                    if (arrNameValuePair.Length == 2)
                    {
                        DicConnectionString.Add(arrNameValuePair[0], arrNameValuePair[1]);
                    }
                }
            }            
        }


        private Dictionary<string, string> DicConnectionString;
        /// <summary>
        /// find the connection string in the db.conf by connectionKey
        /// </summary>
        /// <param name="connectionKey"></param>
        /// <returns>null if can find connectionString by connectionKey</returns>
        public DbConnection GetDbConnection(string connectionKey)
        {
            string sConnectionString = string.Empty;
            if (DicConnectionString.TryGetValue(connectionKey, out sConnectionString))
            {
                MySqlConnection con = new MySqlConnection(sConnectionString);
                return con;
            }
            else
            {
                return null;
            }
        }

        
    }
}
