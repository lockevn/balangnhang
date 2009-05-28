using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using TalaAPI.Business;
using TalaAPI.Lib;
using TalaAPI.XMLRenderOutput;

namespace TestBusiness
{
    class MyClass : APIDataEntry
    {
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string FieldProperty
        {
            get { return "nestedvalue"; }
        }

        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public string AttribFieldProperty
        {
            get { return "atrribvalue"; }
        }

        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public Card MyCard
        {
            get 
            {
                Card c = new Card("13", "c");
                return c;                
            }
        }


        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric)]
        public List<APIDataEntry> ListOfCard
        {
            get
            {
                List<APIDataEntry> list = new List<APIDataEntry>();
                Card c1 = new Card("01", "c");
                Card c2 = new Card("02", "c");
                list.Add(c1);
                list.Add(c2);

                return list;
            }
        }

        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.Array)]
        public Card[] CardArray
        {
            get
            {                
                Card c1 = new Card("11", "c");
                Card c2 = new Card("12", "c");
                Card[] array = { c1, c2};

                return array;
            }
        }
    }


    class Program
    {        
        static void Main(string[] args)
        {
            MyClass mc = new MyClass();
  
            Console.WriteLine(mc.ToXMLString());
            Console.ReadLine();
        }
    }
}
