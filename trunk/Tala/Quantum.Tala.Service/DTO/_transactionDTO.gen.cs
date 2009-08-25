using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for transactionDTO.
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("money_transaction","id",true)]
	 public sealed partial class transactionDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string MOUNTPOINT_FLD = "mountpoint";
		public const string MOUNTPOINT1_FLD = "mountpoint1";
		public const string MOUNTPOINT2_FLD = "mountpoint2";
		public const string MOUNTPOINT3_FLD = "mountpoint3";
		public const string AMOUNT_FLD = "amount";
		public const string DESC_FLD = "desc";
		public const string META_FLD = "meta";
		public const string META1_FLD = "meta1";
		public const string META2_FLD = "meta2";
		public const string META3_FLD = "meta3";
		public const string TYPE_FLD = "type";
        public const string STATUS_FLD = "status";
		public const string DT_FLD = "dt";
		

		private int m_nid;
		private int m_nmountpoint;
		private int m_nmountpoint1;
		private int m_nmountpoint2;
		private int m_nmountpoint3;
		private int m_namount;
		private string m_sdesc;
		private string m_smeta;
		private string m_smeta1;
		private string m_smeta2;
		private string m_smeta3;
		private int m_ntype;
        private string m_sstatus;
		private object m_odt;
		
		public transactionDTO()
		{
		}
		
		
		
		[PersistenceProperty("id",true,true)]
		public int id
		{
			get
			{
				return m_nid;
			}
			set
			{
				m_htIsNull["id"] = false;
				m_nid = value;
			}
		}
		
		[PersistenceProperty("mountpoint",false,false)]
		public int mountpoint
		{
			get
			{
				return m_nmountpoint;
			}
			set
			{
				m_htIsNull["mountpoint"] = false;
				m_nmountpoint = value;
			}
		}
		
		[PersistenceProperty("mountpoint1",false,false)]
		public int mountpoint1
		{
			get
			{
				return m_nmountpoint1;
			}
			set
			{
				m_htIsNull["mountpoint1"] = false;
				m_nmountpoint1 = value;
			}
		}
		
		[PersistenceProperty("mountpoint2",false,false)]
		public int mountpoint2
		{
			get
			{
				return m_nmountpoint2;
			}
			set
			{
				m_htIsNull["mountpoint2"] = false;
				m_nmountpoint2 = value;
			}
		}
		
		[PersistenceProperty("mountpoint3",false,false)]
		public int mountpoint3
		{
			get
			{
				return m_nmountpoint3;
			}
			set
			{
				m_htIsNull["mountpoint3"] = false;
				m_nmountpoint3 = value;
			}
		}
		
		[PersistenceProperty("amount",false,false)]
		public int amount
		{
			get
			{
				return m_namount;
			}
			set
			{
				m_htIsNull["amount"] = false;
				m_namount = value;
			}
		}
		
		[PersistenceProperty("desc",false,false)]
		public string desc
		{
			get
			{
				return m_sdesc;
			}
			set
			{
				m_htIsNull["desc"] = false;
				m_sdesc = value;
			}
		}
		
		[PersistenceProperty("meta",false,false)]
		public string meta
		{
			get
			{
				return m_smeta;
			}
			set
			{
				m_htIsNull["meta"] = false;
				m_smeta = value;
			}
		}
		
		[PersistenceProperty("meta1",false,false)]
		public string meta1
		{
			get
			{
				return m_smeta1;
			}
			set
			{
				m_htIsNull["meta1"] = false;
				m_smeta1 = value;
			}
		}
		
		[PersistenceProperty("meta2",false,false)]
		public string meta2
		{
			get
			{
				return m_smeta2;
			}
			set
			{
				m_htIsNull["meta2"] = false;
				m_smeta2 = value;
			}
		}
		
		[PersistenceProperty("meta3",false,false)]
		public string meta3
		{
			get
			{
				return m_smeta3;
			}
			set
			{
				m_htIsNull["meta3"] = false;
				m_smeta3 = value;
			}
		}
		
		[PersistenceProperty("type",false,false)]
		public int type
		{
			get
			{
				return m_ntype;
			}
			set
			{
				m_htIsNull["type"] = false;
				m_ntype = value;
			}
		}

        [PersistenceProperty("status", false, false)]
        public string status
        {
            get
            {
                return m_sstatus;
            }
            set
            {
                m_htIsNull["status"] = false;
                m_sstatus = value;
            }
        }

		[PersistenceProperty("dt",false,false)]
		public object dt
		{
			get
			{
				return m_odt;
			}
			set
			{
				m_htIsNull["dt"] = false;
				m_odt = value;
			}
		}

		public string IsNullSerializedString
		{
			get
			{
				return GetIsNullSerializedString();
			}
			set
			{
				SetIsNullSerializedString(value);
			}
		}
	}
}
