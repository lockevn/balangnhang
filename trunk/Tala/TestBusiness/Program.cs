using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using TalaAPI.Business;
using TalaAPI.Lib;

namespace TestBusiness
{
    class TestClass
    {
        bool _IsPlaying;
        /// <summary>
        /// Nếu trường này bằng true, thì sới này đang có ván đang chơi, ván đang diễn ra. Nếu trường này bằng false, ván chưa bắt đầu hoặc đã kết thúc. Lúc này client cần đọc thông tin về kết quả ván chơi trước , ...
        /// </summary>
        public bool IsPlaying
        {
            get
            {
                return _IsPlaying;
            }
            set
            {
                _IsPlaying = value;
            }
        }
    }
    class Program
    {
        
        static void Main(string[] args)
        {
            TestClass t = new TestClass();

            object o = "1";

            bool result;
            result =  (o as string).String01ToBoolSafety();


            bool b = bool.TryParse("0", out result);

            try
            {
                result = Convert.ToBoolean("1");
                result = Convert.ToBoolean("0");
                result = Convert.ToBoolean("");
                result = Convert.ToBoolean(string.Empty);
                result = Convert.ToBoolean(null);
            }
            catch { }



            Card c1 = new Card("01","r");
            Card c2 = new Card("01", "t");
            Card c3 = new Card("01", "r");

            c2.Pos = 2;
            c3.Pos = 3;
            Card c4 = null;


            List<Card> baitrentay = new List<Card>();

            baitrentay.Add(c1);
            baitrentay.Add(c2);

            b = baitrentay.Contains(c3);

            if (c4 == null)
            {
                Console.WriteLine("C1 is null");
            }

            Card[] arrCard = { c1, c2 , c3};
            
            Console.WriteLine(c1 == c2);
            Console.WriteLine(c1 > c2);
            Console.WriteLine(c1 < c2);

            arrCard.IsValidPhom();            
            Console.WriteLine(arrCard.ToTalaString());

            Console.ReadLine();
        }
    }
}
