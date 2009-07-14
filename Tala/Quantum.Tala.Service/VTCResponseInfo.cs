using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service
{
    class VTCResponseInfo
    {
        public bool ParseOK { get; set; }
        string[] Items { get; set; }

        public VTCResponseInfo()
        {
            ParseOK = false;
        }

        public static VTCResponseInfo Parse(string s)
        {
            VTCResponseInfo oRes = new VTCResponseInfo();
            
            string[] arr = s.Split(new char[]{'|'}, StringSplitOptions.RemoveEmptyEntries);

            if (arr == null || arr.Length < 1)
            {
                oRes.ParseOK = false;
            }
            else
            {
                oRes.Items = arr;
                oRes.ParseOK = true;
            }

            return oRes;
        }

        public string GetItem(int n, params string[] ReturnIfFail)
        {
            if (n < Items.Length)
            {
                return Items[n];
            }
            else
            {
                return (ReturnIfFail == null || ReturnIfFail.Length < 1) ? null : ReturnIfFail[0];
            }
        }
    }
}
